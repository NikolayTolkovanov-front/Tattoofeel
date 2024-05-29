<?php

use yii\db\Migration;

/**
 * Class m200520_112349_change_title_short_product
 */
class m200520_112349_change_title_short_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%product}}', 'title_short');
        $this->addColumn('{{%product}}', 'title_short', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_112349_change_title_short_product cannot be reverted.\n";

        return false;
    }
    */
}
