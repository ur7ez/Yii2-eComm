<?php

namespace frontend\controllers;

use common\models\Order;
use common\models\OrderAddress;
use common\models\Product;
use common\models\User;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use Yii;
use common\models\CartItem;
use yii\filters\ContentNegotiator;
use frontend\base\Controller;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add', 'create-order', 'submit-payment', 'change-quantity',],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST', 'DELETE'],
                    'create-order' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $cartItems = CartItem::getItemsForUser(currUserId());
        return $this->render('index', [
            'items' => $cartItems,
        ]);
    }

    public function actionAdd()
    {
        $id = Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException('Product does not exist');
        }
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $found = false;
            foreach ($cartItems as &$cartItem) {
                if ($cartItem['id'] == $id) {
                    $cartItem['quantity']++;
                    $found = true;
                    break;
                }
            }
            unset($cartItem);
            if (!$found) {
                $cartItem = [
                    'id' => $id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => $product->price,
                    'quantity' => 1,
                    'total_price' => $product->price,
                ];
                $cartItems[] = $cartItem;
            }

            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);

        } else {
            $userId = currUserId();
            $cartItem = CartItem::find()->userId($userId)->productId($id)->one();
            if ($cartItem instanceof CartItem) {
                $cartItem->quantity++;
            } else {
                $cartItem = new CartItem();
                $cartItem->product_id = $id;
                $cartItem->created_by = $userId;
                $cartItem->quantity = 1;
            }
            if ($cartItem->save()) {
                return [
                    'success' => true
                ];
            }

            return [
                'success' => false,
                'errors' => $cartItem->errors,
            ];
        }
    }

    public function actionDelete($id)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as $i => $cartItem) {
                if ($cartItem['id'] == $id) {
                    array_splice($cartItems, $i, 1);
                    break;
                }
            }
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            CartItem::deleteAll(['product_id' => $id, 'created_by' => currUserId()]);
        }
        return $this->redirect(['index']);
    }

    public function actionChangeQuantity()
    {
        $id = Yii::$app->request->post('id');
        $product = Product::find()->id($id)->published()->one();
        if (!$product) {
            throw new NotFoundHttpException('Product does not exist');
        }
        $quantity = max(1, Yii::$app->request->post('quantity'));
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            foreach ($cartItems as &$cartItem) {
                if ($cartItem['id'] == $id) {
                    $cartItem['quantity'] = $quantity;
                    break;
                }
            }
            unset($cartItem);
            Yii::$app->session->set(CartItem::SESSION_KEY, $cartItems);
        } else {
            $cartItem = CartItem::find()->userId(currUserId())->productId($id)->one();
            if ($cartItem instanceof CartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        }
        return [
            'quantity' => CartItem::getTotalQuantityForUser(currUserId()),
            'item_price' => Yii::$app->formatter->asCurrency(CartItem::getTotalPriceForItemForUser($id, currUserId())),
        ];
    }

    public function actionCheckout()
    {
        $cartItems = CartItem::getItemsForUser(currUserId());
        $productQuantity = CartItem::getTotalQuantityForUser(currUserId());
        $totalPrice = CartItem::getTotalPriceForUser(currUserId());

        if (empty($cartItems)) {
            return $this->redirect([Yii::$app->homeUrl]);
        }

        $order = new Order();

        $order->total_price = $totalPrice;
        $order->status = Order::STATUS_DRAFT;
        $order->created_at = time();
        $order->created_by = currUserId();
        $postData = Yii::$app->request->post();

        $transaction = \Yii::$app->db->beginTransaction();
        if (
            $order->load($postData)
            && $order->save()
            && $order->saveAddress($postData)
            && $order->saveOrderItems()
        ) {
            $transaction->commit();

            CartItem::clearCartItems(currUserId());

            return $this->render('pay-now', [
                'order' => $order,
            ]);
        }

        $orderAddress = new OrderAddress();
        if (!isGuest()) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $userAddress = $user->getAddress();

            $order->status = Order::STATUS_DRAFT;
            $order->firstname = $user->firstname;
            $order->lastname = $user->lastname;
            $order->email = $user->email;

            // $orderAddress->order_id = $order->id;
            $orderAddress->address = $userAddress->address;
            $orderAddress->city = $userAddress->city;
            $orderAddress->state = $userAddress->state;
            $orderAddress->country = $userAddress->country;
            $orderAddress->zipcode = $userAddress->zipcode;
        }

        return $this->render('checkout', [
            'order' => $order,
            'orderAddress' => $orderAddress,
            'cartItems' => $cartItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice,
        ]);
    }

    /**
     * @param $orderId
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \PayPalHttp\HttpException
     * @throws \PayPalHttp\IOException
     */
    public function actionSubmitPayment($orderId)
    {
        $where = ['id' => $orderId, 'status' => Order::STATUS_DRAFT];
        if (!isGuest()) {
            $where['created_by'] = currUserId();
        }
        $order = Order::findOne($where);
        if (!$order) {
            throw new NotFoundHttpException();
        }
        $postData = Yii::$app->request->post();
        $paypalOrderId = $postData['orderID'];
        $exists = Order::find()->andWhere(['paypal_order_id' => $paypalOrderId])->exists();
        if ($exists) {
            throw new BadRequestHttpException();
        }

        $environment = new SandboxEnvironment(param('paypalClientId'), param('paypalSecret'));
        $client = new PayPalHttpClient($environment);
        $response = $client->execute(new OrdersGetRequest($paypalOrderId));

        if ($response->statusCode === 200) {
            $order->paypal_order_id = $paypalOrderId;
            $paidAmount =0;
            foreach ($response->result->purchase_units as $purchase_unit) {
                if ($purchase_unit->amount->currency_code === 'USD') {
                    $paidAmount += $purchase_unit->amount->value;
                }
            }
            if ($paidAmount === (float) $order->total_price && $response->result->status === 'COMPLETED') {
                $order->status = Order::STATUS_COMPLETED;
            } else {
                $order->status = Order::STATUS_FAILED;
            }
            // PayPal transaction ID:
            $order->transaction_id = $response->result->purchase_units[0]->payments->captures[0]->id;
            if ($order->save()) {
                if (!$order->sendEmailToVendor()) {
                    Yii::error('Email to the vendor is not sent');
                }
                if (!$order->sendEmailToCustomer()) {
                    Yii::error('Email to the customer is not sent');
                }
                return [
                    'success' => true,
                ];
            }

            Yii::error("Order was not saved. Data: "
                . VarDumper::dumpAsString($order->toArray()) .
                '. Errors: ' . VarDumper::dumpAsString($order->errors));
        }
        throw new BadRequestHttpException();
        // TODO: validate transaction ID.
        // It must not be used and must be valid transaction ID in PayPal
    }
}