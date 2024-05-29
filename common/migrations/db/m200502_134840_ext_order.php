<?php

use yii\db\Migration;

/**
 * Class m200502_134840_ext_order
 */
class m200502_134840_ext_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'comment', $this->text());
        $this->addColumn('{{%user_client_order}}', 'address_delivery', $this->text());
        $this->addColumn('{{%user_client_order}}', 'sum_delivery', $this->double());
        $this->addColumn('{{%user_client_order}}', 'sum_buy', $this->double());
        $this->addColumn('{{%user_client_order}}', 'date_pay', $this->integer());
        $this->addColumn('{{%user_client_order}}', 'pay_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_order}}', 'comment');
        $this->dropColumn('{{%user_client_order}}', 'address_delivery');
        $this->dropColumn('{{%user_client_order}}', 'sum_delivery');
        $this->dropColumn('{{%user_client_order}}', 'sum_buy');
        $this->dropColumn('{{%user_client_order}}', 'date_pay');
        $this->dropColumn('{{%user_client_order}}', 'pay_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200502_134840_ext_order cannot be reverted.\n";

        return false;
    }
    */
}
