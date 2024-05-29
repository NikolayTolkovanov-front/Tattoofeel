<?php

use yii\db\Migration;

/**
 * Class m201112_074358_add_category_seo_fields
 */
class m201112_074358_add_product_category_seo_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_category}}', 'seo_title', $this->text());
        $this->addColumn('{{%product_category}}', 'seo_desc', $this->text());
        $this->addColumn('{{%product_category}}', 'seo_keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_category}}', 'seo_title');
        $this->dropColumn('{{%product_category}}', 'seo_desc');
        $this->dropColumn('{{%product_category}}', 'seo_keywords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_074358_add_category_seo_fields cannot be reverted.\n";

        return false;
    }
    */
}
