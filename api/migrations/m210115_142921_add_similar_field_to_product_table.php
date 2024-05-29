<?php

use yii\db\Migration;

/**
 * Class m210115_142921_add_similar_field_to_product_table
 */
class m210115_142921_add_similar_field_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'similar', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'similar');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_110121_add_some_fields_to_user_client_order cannot be reverted.\n";

        return false;
    }
    */
}
