<?php

use yii\db\Migration;

/**
 * Class m201221_110121_add_some_fields_to_user_client_order
 */
class m210219_163123_add_courier_time_interval_field_to_user_client_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'courier_time_interval', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_order}}', 'courier_time_interval');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_110121_add_some_fields_to_user_client_order cannot be reverted.\n";

        return false;
    }
    */
}
