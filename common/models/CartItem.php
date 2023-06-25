<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%cart_items}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int $created_by
 *
 * @property User $createdBy
 * @property Product $product
 */
class CartItem extends \yii\db\ActiveRecord
{
    const SESSION_KEY = 'CART_ITEMS';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cart_items}}';
    }

    /**
     * @param int|null $currUserId
     * @return int
     */
    public static function getTotalQuantityForUser(?int $currUserId)
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(self::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity'];
            }
        } else {
            $sum = self::findBySql(
                "SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId",
                ['userId' => $currUserId]
            )->scalar();
        }
        return $sum;
    }

    public static function getTotalPriceForUser(?int $currUserId): float
    {
        $cartItems = self::getItemsForUser($currUserId);
        $sum = 0;
        foreach ($cartItems as $cartItem) {
            $sum += $cartItem['quantity'] * $cartItem['price'];
        }
        return $sum;
    }

    /**
     * @param int|null $currUserId
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getItemsForUser(?int $currUserId): array
    {
        if (isGuest()) {
            $cartItems = Yii::$app->session->get(self::SESSION_KEY, []);
        } else {
            $cartItems = self::findBySql(
                'SELECT 
                   c.product_id AS id,
                   p.image,
                   p.name,
                   p.price,
                   c.quantity,
                   p.price * c.quantity AS total_price
                FROM cart_items c
                         LEFT JOIN products p on c.product_id = p.id
                WHERE c.created_by = :userId',
                ['userId' => $currUserId]
            )
                ->asArray()
                ->all();
        }
        return $cartItems;
    }

    /**
     * @param int|null $currUserId
     * @return void
     */
    public static function clearCartItems(?int $currUserId): void
    {
        if (isGuest()) {
            Yii::$app->session->remove(self::SESSION_KEY);
        } else {
            self::deleteAll(['created_by' => $currUserId]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'created_by'], 'required'],
            [['product_id', 'quantity', 'created_by'], 'integer'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'quantity' => 'Quantity',
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
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\ProductsQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\CartItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CartItemQuery(get_called_class());
    }
}