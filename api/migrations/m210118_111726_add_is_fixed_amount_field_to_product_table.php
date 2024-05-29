<?php

use yii\db\Migration;

/**
 * Class m210118_111726_add_is_fixed_amount_field_to_product_table
 */
class m210118_111726_add_is_fixed_amount_field_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'is_fixed_amount', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'is_fixed_amount');
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
