<?php

namespace common\models;

use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%products}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image
 * @property float $price
 * @property int $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property CartItem[] $cartItems
 * @property User $createdBy
 * @property OrderItem[] $orderItems
 * @property User $updatedBy
 */
class Product extends \yii\db\ActiveRecord
{
    public const STATUS_UNLISTED = 0;
    public const STATUS_PUBLISHED = 1;

    /** @var $imageFile UploadedFile | null */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%products}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'status'], 'required'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['imageFile'], 'image', 'extensions' => 'png, jpg, jpeg, webp', 'maxSize' => 10 * 1024 * 1024],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 2000],
            ['status', 'default', 'value' => self::STATUS_UNLISTED],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'image' => Yii::t('app', 'Product Image'),
            'imageFile' => Yii::t('app', 'Product Image File'),
            'price' => Yii::t('app', 'Price'),
            'status' => Yii::t('app', 'Published'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    public function getStatusLabels(): array
    {
        return [
            self::STATUS_UNLISTED => Yii::t('app', 'Unlisted'),
            self::STATUS_PUBLISHED => Yii::t('app', 'Published'),
        ];
    }

    /**
     * Gets query for [[CartItem]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\CartItemQuery
     */
    public function getCartItems()
    {
        return $this->hasMany(CartItem::class, ['product_id' => 'id']);
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
     * Gets query for [[OrderItem]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ProductQuery(get_called_class());
    }

    /**
     * @param bool $runValidation
     * @param $attributeNames
     * @return bool
     * @throws Exception
     */
    public function save($runValidation = true, $attributeNames = null): bool
    {
        if ($this->imageFile) {
            $this->image = 'products/' . Yii::$app->security->generateRandomString()
                . '/' . $this->imageFile->name;
        }
        $transaction = Yii::$app->db->beginTransaction();
        $saved = $transaction && parent::save($runValidation, $attributeNames);

        if ($saved && $this->imageFile) {
            $fullPath = Yii::getAlias('@frontend/web/storage/' . $this->image);
            if (!FileHelper::createDirectory(dirname($fullPath)) || !$this->imageFile->saveAs($fullPath)) {
                $transaction->rollBack();
                return false;
            }
        }
        $transaction->commit();
        return $saved;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return self::formatImageUrl($this->image);
    }

    /**
     * @param $imgPath
     * @return string
     */
    public static function formatImageUrl ($imgPath): string
    {
        if ($imgPath) {
            return param('frontendUrl') . '/storage/' . $imgPath;
        }
        return param('frontendUrl') . '/img/no_image_available.svg';
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return \yii\helpers\StringHelper::truncateWords(strip_tags($this->description), 30);
    }

    /**
     * @throws ErrorException
     */
    public function afterDelete()
    {
        parent::afterDelete();
        if ($this->image) {
            $dir = Yii::getAlias('@frontend/web/storage/') . dirname($this->image);
            FileHelper::removeDirectory($dir);
        }
    }
}