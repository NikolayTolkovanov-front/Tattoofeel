<?php

use yii\db\Migration;

/**
 * Class m200709_081555_p_config_order
 */
class m200709_081555_p_config_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'order_config', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'order_config');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200709_081555_p_config_order cannot be reverted.\n";

        return false;
    }
    */
}
