<?php

use yii\db\Migration;

/**
 * Class m200504_085751_ext_client_profile_client_ms_id
 */
class m200504_085751_ext_client_profile_client_ms_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_profile}}', 'client_ms_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_profile}}', 'client_ms_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200504_085751_ext_client_profile_client_ms_id cannot be reverted.\n";

        return false;
    }
    */
}
