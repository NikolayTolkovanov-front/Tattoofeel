<?php

use yii\db\Migration;

/**
 * Class m200304_150518_user_client_ext
 */
class m200304_150518_user_client_ext extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%user_client_order}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'order_ms_id' => $this->string(),
            'date' => $this->integer()->notNull(),
            'status_pay' => $this->tinyInteger(4),
            'status_delivery' => $this->tinyInteger(2),
            'isCart' => $this->tinyInteger(2),
            'status_ms_sync' => $this->integer()->defaultValue(0),
            'client_created_at' => $this->integer(),
            'client_updated_at' => $this->integer(),
            'client_created_by' => $this->integer(),
            'client_updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->createTable('{{%user_client_order__product}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'count' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(),
            'currency_iso_code' => $this->string()->notNull(),
        ]);

        $this->createTable('{{%user_client_product_deferred}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'client_created_at' => $this->integer(),
            'client_updated_at' => $this->integer(),
            'client_created_by' => $this->integer(),
            'client_updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);


        $this->addForeignKey('fk_user_client_order__product__product',
            '{{%user_client_order__product}}', 'product_id',
            '{{%product}}', 'id',
            'cascade', 'cascade');

        $this->addForeignKey('fk_user_client_order__product__order',
            '{{%user_client_order__product}}', 'order_id',
            '{{%user_client_order}}', 'id',
            'cascade', 'cascade');


        $this->addForeignKey('fk_user_client_product_deferred__product',
            '{{%user_client_product_deferred}}', 'product_id',
            '{{%product}}', 'id',
            'cascade', 'cascade');

        $this->addForeignKey('fk_user_client_product_deferred__user',
            '{{%user_client_product_deferred}}', 'user_id',
            '{{%user_client}}', 'id',
            'cascade', 'cascade');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('fk_user_client_order__product__product', '{{%user_client_order__product}}');
        $this->dropForeignKey('fk_user_client_order__product__order', '{{%user_client_order__product}}');

        $this->dropForeignKey('fk_user_client_product_deferred__product', '{{%user_client_product_deferred}}');
        $this->dropForeignKey('fk_user_client_product_deferred__user','{{%user_client_product_deferred}}');


        $this->dropTable('{{%user_client_order}}');
        $this->dropTable('{{%user_client_order__product}}');
        $this->dropTable('{{%user_client_product_deferred}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200304_150518_user_client_order cannot be reverted.\n";

        return false;
    }
    */
}
