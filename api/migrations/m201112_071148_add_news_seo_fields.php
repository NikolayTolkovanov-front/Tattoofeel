<?php

use yii\db\Migration;

/**
 * Class m201112_071148_add_news_seo_fields
 */
class m201112_071148_add_news_seo_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%news}}', 'seo_title', $this->text());
        $this->addColumn('{{%news}}', 'seo_desc', $this->text());
        $this->addColumn('{{%news}}', 'seo_keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%news}}', 'seo_title');
        $this->dropColumn('{{%news}}', 'seo_desc');
        $this->dropColumn('{{%news}}', 'seo_keywords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_071148_add_news_seo_fields cannot be reverted.\n";

        return false;
    }
    */
}
