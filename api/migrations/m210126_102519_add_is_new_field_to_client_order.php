<?php

use yii\db\Migration;

/**
 * Class m210126_102519_add_is_new_field_to_client_order
 */
class m210126_102519_add_is_new_field_to_client_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->addColumn('{{%user_client_order}}', 'is_new', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->dropColumn('{{%user_client_order}}', 'is_new');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_102519_add_is_new_field_to_client_order cannot be reverted.\n";

        return false;
    }
    */
}
