<?php

use yii\db\Migration;

/**
 * Class m230212_080553_add_crm_percent_discount_field_to_client_order_product
 */
class m230212_080553_add_crm_percent_discount_field_to_client_order_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order__product}}', 'crm_percent_discount', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_order__product}}', 'crm_percent_discount');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220622_134114_add_thumbnail_path_2_field_to_slider_main cannot be reverted.\n";

        return false;
    }
    */
}
