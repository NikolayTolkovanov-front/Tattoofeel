<?php

use yii\db\Migration;

/**
 * Class m200302_153442_product_ext_1
 */
class m200302_153442_product_ext_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product}}', 'view_count', $this->integer());
        $this->addColumn('{{%product}}', 'is_new', $this->tinyInteger()->notNull()->defaultValue(0));
        $this->addColumn('{{%product}}', 'is_new_at', $this->integer());
        $this->addColumn('{{%product}}', 'video_code', $this->string(1024));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product}}', 'view_count');
        $this->dropColumn('{{%product}}', 'is_new');
        $this->dropColumn('{{%product}}', 'is_new_at');
        $this->dropColumn('{{%product}}', 'video_code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200302_153442_product_ext_1 cannot be reverted.\n";

        return false;
    }
    */
}
