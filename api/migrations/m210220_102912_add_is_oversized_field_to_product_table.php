<?php

use yii\db\Migration;

/**
 * Class m210220_102912_add_is_oversized_field_to_product_table
 */
class m210220_102912_add_is_oversized_field_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'is_oversized', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'is_oversized');
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
