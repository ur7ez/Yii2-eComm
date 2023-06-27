<?php
namespace common\i18n;
use common\models\Order;
use yii\bootstrap5\Html;

/**
 * @package
 */
class Formatter extends \yii\i18n\Formatter
{
    /**
     * @param $status
     * @return string
     */
    public function asOrderStatus ($status)
    {
        if ($status == Order::STATUS_COMPLETED) {
            return Html::tag('span', 'Paid', ['class' => 'badge badge-success']);
        }
        if ($status == Order::STATUS_DRAFT) {
            return Html::tag('span', 'Unpaid', ['class' => 'badge badge-secondary']);
        }
        return Html::tag('span', 'Failed', ['class' => 'badge badge-danger']);
    }
}