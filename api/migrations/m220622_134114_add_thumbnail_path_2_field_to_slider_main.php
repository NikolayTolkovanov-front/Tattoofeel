<?php

use yii\db\Migration;

/**
 * Class m220622_134114_add_thumbnail_path_2_field_to_slider_main
 */
class m220622_134114_add_thumbnail_path_2_field_to_slider_main extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%slider_main}}', 'thumbnail_path_2', $this->string(128)->after('thumbnail_path'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%slider_main}}', 'thumbnail_path_2');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220622_134114_add_thumbnail_path_2_field_to_slider_main cannot be reverted.\n";

        return false;
    }
    */
}
