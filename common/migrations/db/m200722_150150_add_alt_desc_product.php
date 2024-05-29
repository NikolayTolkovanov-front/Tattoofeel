<?php

use yii\db\Migration;

/**
 * Class m200722_150150_add_alt_desc_product
 */
class m200722_150150_add_alt_desc_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'alt_desc', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'alt_desc');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200722_150150_add_alt_desc_product cannot be reverted.\n";

        return false;
    }
    */
}
