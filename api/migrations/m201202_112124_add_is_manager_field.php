<?php

use yii\db\Migration;

/**
 * Class m201202_112124_add_is_manager_field
 */
class m201202_112124_add_is_manager_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client}}', 'is_manager', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client}}', 'is_manager');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201202_112124_add_is_manager_field cannot be reverted.\n";

        return false;
    }
    */
}
