<?php

use yii\db\Migration;

/**
 * Class m230822_093943_add_product_gift
 */
class m230822_093943_add_product_gift extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_gift}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'quantity' => $this->integer(),
            'coupon_id' => $this->integer(),
        ]);

        $this->addForeignKey('fk_product_gift_product', '{{%product_gift}}', 'product_id', '{{%product}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_product_gift_coupon', '{{%product_gift}}', 'coupon_id', '{{%coupons}}', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230822_093943_add_product_gift cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_093943_add_product_gift cannot be reverted.\n";

        return false;
    }
    */
}
