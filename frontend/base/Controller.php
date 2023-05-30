<?php
namespace frontend\base;

use Yii;
use common\models\CartItem;

class Controller extends \yii\web\Controller
{
    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'];
            }
        } else {
            $sum = CartItem::findBySql(
                "SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId",
                ['userId' => currUserId()]
            )->scalar();
        }
        $this->view->params['cartItemCount'] = $sum;
        return parent::beforeAction($action);
    }
}