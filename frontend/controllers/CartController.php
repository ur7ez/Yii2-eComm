<?php

namespace frontend\controllers;

use common\models\Product;
use Yii;
use common\models\CartItem;
use yii\filters\ContentNegotiator;
use frontend\base\Controller;
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
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            // get items from Session
            $cartItems = [];
        } else {
            $cartItems = CartItem::findBySql(
            "SELECT 
                   c.product_id AS id,
                   p.image,
                   p.name,
                   p.price,
                   c.quantity,
                   p.price * c.quantity AS total_price
                FROM cart_items c
                         LEFT JOIN products p on c.product_id = p.id
                WHERE c.created_by = :userId",
                ['userId' => Yii::$app->user->id]
            )
                ->asArray()
                ->all();
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
        if (Yii::$app->user->isGuest) {
            // TODO: save in Session

        } else {
            $userId = Yii::$app->user->id;
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
}