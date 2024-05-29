<?php

use yii\db\Migration;

/**
 * Class m200708_155509_p_category_order
 */
class m200708_155509_p_category_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_category}}', 'order', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_category}}', 'order');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200708_155509_p_category_order cannot be reverted.\n";

        return false;
    }
    */
}
