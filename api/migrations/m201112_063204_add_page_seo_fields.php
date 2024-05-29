<?php

use yii\db\Migration;

/**
 * Class m201112_063204_add_page_seo_fields
 */
class m201112_063204_add_page_seo_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%page}}', 'seo_title', $this->text());
        $this->addColumn('{{%page}}', 'seo_desc', $this->text());
        $this->addColumn('{{%page}}', 'seo_keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%page}}', 'seo_title');
        $this->dropColumn('{{%page}}', 'seo_desc');
        $this->dropColumn('{{%page}}', 'seo_keywords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201112_063204_add_page_seo_fields cannot be reverted.\n";

        return false;
    }
    */
}
