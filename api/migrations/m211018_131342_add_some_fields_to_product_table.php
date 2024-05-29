<?php

use yii\db\Migration;

/**
 * Class m211018_131342_add_some_fields_to_product_table
 */
class m211018_131342_add_some_fields_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'is_discount', $this->tinyInteger()->notNull()->defaultValue(0));
        $this->addColumn('{{%product}}', 'is_super_price', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'is_discount');
        $this->dropColumn('{{%product}}', 'is_super_price');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210118_111726_add_is_fixed_amount_field_to_product_table cannot be reverted.\n";

        return false;
    }
    */
}
