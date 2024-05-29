<?php

use yii\db\Migration;

/**
 * Class m201112_072321_add_stock_seo_fields
 */
class m201112_072321_add_stock_seo_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%stock}}', 'seo_title', $this->text());
        $this->addColumn('{{%stock}}', 'seo_desc', $this->text());
        $this->addColumn('{{%stock}}', 'seo_keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%stock}}', 'seo_title');
        $this->dropColumn('{{%stock}}', 'seo_desc');
        $this->dropColumn('{{%stock}}', 'seo_keywords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_072321_add_stock_seo_fields cannot be reverted.\n";

        return false;
    }
    */
}
