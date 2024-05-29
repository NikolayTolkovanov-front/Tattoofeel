<?php

use yii\db\Migration;

/**
 * Class m230807_083843_add_showroom_workhours
 */
class m230807_083843_add_showroom_workhours extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%subdomains}}', 'work_hours_showroom', $this->text());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%subdomains}}', 'work_hours_showroom');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230807_083843_add_showroom_workhours cannot be reverted.\n";

        return false;
    }
    */
}
