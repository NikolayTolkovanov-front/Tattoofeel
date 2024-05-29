<?php

use yii\db\Migration;

/**
 * Class m210124_175520_add_delivery_city_field_to_client_order
 */
class m210124_175520_add_delivery_city_field_to_client_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->addColumn('{{%user_client_order}}', 'delivery_city', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->dropColumn('{{%user_client_order}}', 'delivery_city');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210124_175520_add_delivery_city_field_to_client_order cannot be reverted.\n";

        return false;
    }
    */
}
