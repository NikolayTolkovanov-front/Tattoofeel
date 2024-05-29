<?php

use yii\db\Migration;

/**
 * Class m210503_123756_add_manager_sitename_col
 */
class m210503_123756_add_manager_sitename_col extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_profile}}', 'ms_owner_name_at_site', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->dropColumn('{{%user_client_profile}}', 'ms_owner_name_at_site');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210503_123756_add_manager_sitename_col cannot be reverted.\n";

        return false;
    }
    */
}
