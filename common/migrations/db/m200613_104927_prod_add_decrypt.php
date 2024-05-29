<?php

use yii\db\Migration;

/**
 * Class m200613_104927_prod_add_decrypt
 */
class m200613_104927_prod_add_decrypt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'config_decrypt', $this->string(1024));
        $this->addColumn('{{%product}}', 'config_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'config_decrypt');
        $this->dropColumn('{{%product}}', 'config_name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200613_104927_prod_add_dycript cannot be reverted.\n";

        return false;
    }
    */
}
