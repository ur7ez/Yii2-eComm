<?php

namespace common\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property int $id
 * @property float $total_price
 * @property int $status
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string|null $transaction_id
 * @property string|null $paypal_order_id
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property User $createdBy
 * @property OrderAddress $orderAddress
 * @property OrderItem[] $orderItems
 */
class Order extends \yii\db\ActiveRecord
{
    public const STATUS_DRAFT = 0;
    public const STATUS_COMPLETED = 1;
    public const STATUS_FAILED = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_price', 'status', 'firstname', 'lastname', 'email'], 'required'],
            [['total_price'], 'number'],
            [['email'], 'email'],
            [['status', 'created_at', 'created_by'], 'integer'],
            [['firstname', 'lastname'], 'string', 'max' => 45],
            [['paypal_order_id'], 'string', 'max' => 55],
            [['email', 'transaction_id'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total_price' => 'Total Price',
            'status' => 'Status',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'Email',
            'transaction_id' => 'Transaction ID',
            'paypal_order_id' => 'PayPal Order ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[OrderAddress]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderAddressQuery
     */
    public function getOrderAddress()
    {
        return $this->hasOne(OrderAddress::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderItem]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\OrderQuery(get_called_class());
    }

    /**
     * @return true
     * @throws Exception
     */
    public function saveOrderItems(): bool
    {
        $cartItems = CartItem::getItemsForUser(currUserId());
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->product_name = $cartItem['name'];
            $orderItem->product_id = $cartItem['id'];
            $orderItem->unit_price = $cartItem['price'];
            $orderItem->order_id = $this->id;
            $orderItem->quantity = $cartItem['quantity'];
            if (!$orderItem->save()) {
                throw new Exception('Order item was not saved: ' . implode('<br>', $orderItem->getFirstErrors()));
            }
        }
        return true;
    }

    /**
     * @param array|mixed|object $postData
     * @return bool
     * @throws Exception
     */
    public function saveAddress($postData): bool
    {
        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $this->id;
        if ($orderAddress->load($postData) && $orderAddress->save()) {
            return true;
        }
        throw new Exception('Could not save order address: ' . implode('<br>', $orderAddress->getFirstErrors()));
    }

    public function getItemsQuantity()
    {
        $sum = self::findBySql(
            'SELECT SUM(quantity) FROM order_items WHERE order_id = :orderId',
            ['orderId' => $this->id]
        )->scalar();
        return $sum;
    }

    /**
     * @return bool
     */
    public function sendEmailToVendor(): bool
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'order_completed_vendor-html', 'text' => 'order_completed_vendor-text'],
                ['order' => $this]
            )
            ->setFrom([param('supportEmail') => Yii::$app->name . ' robot'])
            ->setTo(param('vendorEmail'))
            ->setSubject('New order has been made at ' . Yii::$app->name)
            ->send();
    }

    /**
     * @return bool
     */
    public function sendEmailToCustomer(): bool
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'order_completed_customer-html', 'text' => 'order_completed_customer-text'],
                ['order' => $this]
            )
            ->setFrom([param('supportEmail') => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Your order is confirmed at ' . Yii::$app->name)
            ->send();
    }
}