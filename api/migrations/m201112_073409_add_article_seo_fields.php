<?php

use yii\db\Migration;

/**
 * Class m201112_073409_add_article_seo_fields
 */
class m201112_073409_add_article_seo_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%article}}', 'seo_title', $this->text());
        $this->addColumn('{{%article}}', 'seo_desc', $this->text());
        $this->addColumn('{{%article}}', 'seo_keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%article}}', 'seo_title');
        $this->dropColumn('{{%article}}', 'seo_desc');
        $this->dropColumn('{{%article}}', 'seo_keywords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_073409_add_article_seo_fields cannot be reverted.\n";

        return false;
    }
    */
}
