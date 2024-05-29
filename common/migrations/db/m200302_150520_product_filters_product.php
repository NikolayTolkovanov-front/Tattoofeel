<?php

use yii\db\Migration;

/**
 * Class m200302_150520_product_filters_product
 */
class m200302_150520_product_filters_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_filters_product}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'product_filters_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_pf_p__product', '{{%product_filters_product}}', 'product_id', '{{%product}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_pf_p__product_filters', '{{%product_filters_product}}', 'product_filters_id', '{{%product_filters}}', 'id', 'cascade', 'cascade');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_pf_p__product', '{{%product_filters_product}}');
        $this->dropForeignKey('fk_pf_p__product_filters', '{{%product_filters_product}}');
        $this->dropTable('product_filters_product');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200302_150520_product_filters_product cannot be reverted.\n";

        return false;
    }
    */
}
