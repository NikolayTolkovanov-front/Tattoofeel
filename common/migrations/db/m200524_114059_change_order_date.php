<?php

use yii\db\Migration;

/**
 * Class m200524_114059_change_order_date
 */
class m200524_114059_change_order_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%user_client_order}}', 'date', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200524_114059_change_order_date cannot be reverted.\n";

        return false;
    }
    */
}
