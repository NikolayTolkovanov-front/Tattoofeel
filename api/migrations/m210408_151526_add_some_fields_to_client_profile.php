<?php

use yii\db\Migration;

/**
 * Class m210408_151526_add_some_fields_to_client_profile
 */
class m210408_151526_add_some_fields_to_client_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_profile}}', 'hide_cash', $this->tinyInteger()->notNull()->defaultValue(0));
        $this->addColumn('{{%user_client_profile}}', 'hide_card', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_profile}}', 'hide_cash');
        $this->dropColumn('{{%user_client_profile}}', 'hide_card');
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
