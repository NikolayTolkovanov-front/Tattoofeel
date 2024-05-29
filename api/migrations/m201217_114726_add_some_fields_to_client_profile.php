<?php

use yii\db\Migration;

/**
 * Class m201217_114726_add_some_fields_to_client_profile
 */
class m201217_114726_add_some_fields_to_client_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_profile}}', 'address_comment', $this->string(255));
        $this->addColumn('{{%user_client_profile}}', 'ms_owner', $this->string(255));
        $this->addColumn('{{%user_client_profile}}', 'ms_bonus', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_profile}}', 'address_delivery_comment');
        $this->dropColumn('{{%user_client_profile}}', 'ms_owner');
        $this->dropColumn('{{%user_client_profile}}', 'ms_bonus');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201217_114726_add_some_fields_to_client_profile cannot be reverted.\n";

        return false;
    }
    */
}
