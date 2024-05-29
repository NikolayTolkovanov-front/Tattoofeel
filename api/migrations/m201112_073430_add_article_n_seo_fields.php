<?php

use yii\db\Migration;

/**
 * Class m201112_073430_add_article_n_seo_fields
 */
class m201112_073430_add_article_n_seo_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%article_n}}', 'seo_title', $this->text());
        $this->addColumn('{{%article_n}}', 'seo_desc', $this->text());
        $this->addColumn('{{%article_n}}', 'seo_keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%article_n}}', 'seo_title');
        $this->dropColumn('{{%article_n}}', 'seo_desc');
        $this->dropColumn('{{%article_n}}', 'seo_keywords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_073430_add_article_n_seo_fields cannot be reverted.\n";

        return false;
    }
    */
}
