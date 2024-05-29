<?php

use yii\db\Migration;

/**
 * Class m201112_075120_add_brand_seo_fields
 */
class m201112_075120_add_brand_seo_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%brand}}', 'seo_title', $this->text());
        $this->addColumn('{{%brand}}', 'seo_desc', $this->text());
        $this->addColumn('{{%brand}}', 'seo_keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%brand}}', 'seo_title');
        $this->dropColumn('{{%brand}}', 'seo_desc');
        $this->dropColumn('{{%brand}}', 'seo_keywords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_075120_add_brand_seo_fields cannot be reverted.\n";

        return false;
    }
    */
}
