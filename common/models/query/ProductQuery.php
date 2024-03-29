<?php
namespace common\models\query;

use common\models\Product;

/**
 * This is the ActiveQuery class for [[\common\models\Product]].
 *
 * @see Product
 */
class ProductQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Product[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Product|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return ProductQuery
     */
    public function published()
    {
        return $this->andWhere(['status' => Product::STATUS_PUBLISHED]);
    }

    /**
     * @param $id
     * @return ProductQuery
     */
    public function id($id): ProductQuery
    {
        return $this->andWhere(['id' => $id]);
    }
}
