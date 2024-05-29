<?php

use yii\db\Migration;

/**
 * Class m210114_101722_change_body_short_field_product_category_table
 */
class m210114_101722_change_body_short_field_product_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%product_category}}', 'body_short', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%product_category}}', 'body_short', $this->string(256));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201222_122702_change_body_short_field_product_table cannot be reverted.\n";

        return false;
    }
    */
}
