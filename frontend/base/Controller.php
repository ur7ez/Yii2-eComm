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
        $this->view->params['cartItemCount'] = CartItem::findBySql(
            "SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId",
            ['userId' => Yii::$app->user->id]
        )->scalar();
        return parent::beforeAction($action);
    }
}