<?php

use yii\db\Migration;

/**
 * Class m230629_213441_change_product_id_fk_on_order_item_table
 */
class m230629_213441_change_product_id_fk_on_order_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // drops foreign key for table `{{%order_items}}`
        $this->dropForeignKey(
            '{{%fk-order_items-product_id}}',
            '{{%order_items}}'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230629_213441_change_product_id_fk_on_order_item_table cannot be reverted.\n";
        return false;
    }
}
