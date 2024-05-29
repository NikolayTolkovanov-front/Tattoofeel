<?php

use yii\db\Migration;

/**
 * Class m210818_134156_add_delivery_days_to_user_client_order
 */
class m210818_134156_add_delivery_days_to_user_client_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'delivery_days', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->dropColumn('{{%user_client_order}}', 'delivery_days');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210503_123756_add_manager_sitename_col cannot be reverted.\n";

        return false;
    }
    */
}
