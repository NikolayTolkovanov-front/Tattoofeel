<?php

use yii\db\Migration;

/**
 * Class m200822_131955_ext_prod_lwh
 */
class m200822_131955_ext_prod_lwh extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'length', $this->double());
        $this->addColumn('{{%product}}', 'width', $this->double());
        $this->addColumn('{{%product}}', 'height', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'length');
        $this->dropColumn('{{%product}}', 'width');
        $this->dropColumn('{{%product}}', 'height');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200822_131955_ext_prod_lwh cannot be reverted.\n";

        return false;
    }
    */
}
