<?php

use yii\db\Migration;

/**
 * Class m200827_094527_etx_order_pvz
 */
class m200827_094527_etx_order_pvz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'address_delivery_pvz', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_order}}', 'address_delivery_pvz');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200827_094527_etx_order_pvz cannot be reverted.\n";

        return false;
    }
    */
}
