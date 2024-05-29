<?php

use yii\db\Migration;

/**
 * Class m230810_101444_add_client_group_to_coupons
 */
class m230810_101444_add_client_group_to_coupons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%coupons}}', 'client_groups', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%coupons}}', 'client_groups');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230810_101444_add_client_group_to_coupons cannot be reverted.\n";

        return false;
    }
    */
}
