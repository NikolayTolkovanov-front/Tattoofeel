<?php

use yii\db\Migration;

/**
 * Class m200520_102808_change
 */
class m200520_102808_change_brandIdType extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%product}}', 'brand_id');
        $this->addColumn('{{%product}}', 'brand_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{%product}', 'brand_id');
        $this->addColumn('{%product}', 'brand_id', $this->integer());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_102808_change cannot be reverted.\n";

        return false;
    }
    */
}
