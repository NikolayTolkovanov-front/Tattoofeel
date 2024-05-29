<?php

use yii\db\Migration;

/**
 * Class m200526_142416_fk_for_client_profile
 */
class m200526_142416_fk_for_client_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey('fk_user_client', '{{%user_client_profile}}', 'user_id', '{{%user_client}}', 'id',  'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_client', '{{%user_client_profile}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200526_142416_fk_for_client_profile cannot be reverted.\n";

        return false;
    }
    */
}
