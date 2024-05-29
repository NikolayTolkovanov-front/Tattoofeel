<?php

use yii\db\Migration;

/**
 * Class m230207_125317_add_phone_field_to_user_client_order
 */

class m230207_125317_add_phone_field_to_user_client_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'phone', $this->string()->after('address_delivery'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_order}}', 'phone');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    }

    public function down()
    {
        echo "m201221_110121_add_some_fields_to_user_client_order cannot be reverted.\n";

        return false;
    }
    */
}
