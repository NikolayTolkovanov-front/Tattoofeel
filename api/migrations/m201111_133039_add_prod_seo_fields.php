<?php

use yii\db\Migration;

/**
 * Class m201111_133039_add_prod_seo_fields
 */
class m201111_133039_add_prod_seo_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'seo_title', $this->text());
        $this->addColumn('{{%product}}', 'seo_desc', $this->text());
        $this->addColumn('{{%product}}', 'seo_keywords', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'seo_title');
        $this->dropColumn('{{%product}}', 'seo_desc');
        $this->dropColumn('{{%product}}', 'seo_keywords');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201111_133039_add_prod_seo_fields cannot be reverted.\n";

        return false;
    }
    */
}
