<?php

use yii\db\Migration;

/**
 * Class m210217_163503_add_some_fields_to_client_profile
 */
class m210217_163503_add_some_fields_to_client_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_profile}}', 'ms_owner_vk', $this->string());
        $this->addColumn('{{%user_client_profile}}', 'ms_owner_whatsapp', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_profile}}', 'ms_owner_vk');
        $this->dropColumn('{{%user_client_profile}}', 'ms_owner_whatsapp');
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
