<?php

use yii\db\Migration;

/**
 * Class m211206_103823_add_index_to_product_table
 */
class m211206_103823_add_index_to_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_product_config_ms_id', '{{%product}}', 'config_ms_id', false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_product_config_ms_id', '{{%product}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210330_095744_add_commission_field_to_user_client_order cannot be reverted.\n";

        return false;
    }
    */
}
