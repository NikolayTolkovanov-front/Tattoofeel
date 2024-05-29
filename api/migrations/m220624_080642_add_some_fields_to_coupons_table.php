<?php

use yii\db\Migration;

/**
 * Class m220624_080642_add_some_fields_to_coupons_table
 */
class m220624_080642_add_some_fields_to_coupons_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%coupons}}', 'is_one_product', $this->tinyInteger()->notNull()->defaultValue(0)->after('is_percent'));
        $this->addColumn('{{%coupons}}', 'is_one_user', $this->tinyInteger()->notNull()->defaultValue(0)->after('is_percent'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'is_one_user');
        $this->dropColumn('{{%product}}', 'is_one_product');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_080642_add_some_fields_to_coupons_table cannot be reverted.\n";

        return false;
    }
    */
}
