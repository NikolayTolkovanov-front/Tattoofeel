<?php

use yii\db\Migration;

/**
 * Class m210330_095744_add_commission_field_to_user_client_order
 */
class m210330_095744_add_commission_field_to_user_client_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'commission', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_order}}', 'commission');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210330_095744_add_commission_field_to_user_client_order cannot be reverted.\n";

        return false;
    }
    */
}
