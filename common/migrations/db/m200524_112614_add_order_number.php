<?php

use yii\db\Migration;

/**
 * Class m200524_112614_add
 */
class m200524_112614_add_order_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'order_ms_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {}

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200524_112614_add cannot be reverted.\n";

        return false;
    }
    */
}
