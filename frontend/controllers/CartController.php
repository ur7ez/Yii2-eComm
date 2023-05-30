<?php

namespace frontend\controllers;

use common\models\Order;
use common\models\OrderAddress;
use common\models\Product;
use common\models\User;
use Yii;
use common\models\CartItem;
use yii\filters\ContentNegotiator;
use frontend\base\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CartController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'only' => ['add'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST', 'DELETE'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (isGuest()) {
            // get items from Session
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        } else {
            $cartItems = CartItem::getItemsForUser(currUserId());
        }
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
        return CartItem::getTotalQuantityForUser(currUserId());
    }

    public function actionCheckout()
    {
        $order = new Order();
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
            $cartItems = CartItem::getItemsForUser(currUserId());
        } else {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
        }

        $productQuantity = CartItem::getTotalQuantityForUser(currUserId());
        $totalPrice = CartItem::getTotalPriceForUser(currUserId());
        return $this->render('checkout', [
            'order' => $order,
            'orderAddress' => $orderAddress,
            'cartItems' => $cartItems,
            'productQuantity' => $productQuantity,
            'totalPrice' => $totalPrice,
        ]);
    }
}