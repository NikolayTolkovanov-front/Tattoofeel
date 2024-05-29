<?php

use yii\db\Migration;

/**
 * Class m200608_104001_ext_page
 */
class m200608_104001_ext_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%page}}', 'thumbnail_desc', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%page}}', 'thumbnail_desc');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200608_104001_ext_page cannot be reverted.\n";

        return false;
    }
    */
}
