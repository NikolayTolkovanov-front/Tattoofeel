<?php

use yii\db\Migration;

/**
 * Class m200729_125403_prod_warranty
 */
class m200729_125403_prod_warranty extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'warranty', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'warranty');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_125403_prod_warranty cannot be reverted.\n";

        return false;
    }
    */
}
