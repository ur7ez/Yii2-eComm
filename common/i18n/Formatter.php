<?php
namespace common\i18n;
use common\models\Order;
use yii\bootstrap5\Html;
use yii\db\Exception;

/**
 * @package
 */
class Formatter extends \yii\i18n\Formatter
{
    /**
     * @param int|string $status order status
     * @return string
     * @throws Exception
     */
    public function asOrderStatus ($status)
    {
        $orderLabels = Order::getStatusLabels();
        switch ($status) {
            case Order::STATUS_COMPLETED:
                return Html::tag('span', $orderLabels[$status], ['class' => 'badge badge-success']);
            case Order::STATUS_PAID:
                return Html::tag('span', $orderLabels[$status], ['class' => 'badge badge-primary']);
            case Order::STATUS_DRAFT:
                return Html::tag('span', $orderLabels[$status], ['class' => 'badge badge-secondary']);
            case Order::STATUS_FAILED:
                return Html::tag('span', $orderLabels[$status], ['class' => 'badge badge-danger']);
            default:
                throw new Exception('Undefined order status: ' . $status);
        }
    }
}