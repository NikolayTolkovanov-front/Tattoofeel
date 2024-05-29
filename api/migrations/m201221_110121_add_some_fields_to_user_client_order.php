<?php

use yii\db\Migration;

/**
 * Class m201221_110121_add_some_fields_to_user_client_order
 */
class m201221_110121_add_some_fields_to_user_client_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'pvz_code', $this->string());
        $this->addColumn('{{%user_client_order}}', 'pvz_info', $this->text());
        $this->addColumn('{{%user_client_order}}', 'track_number', $this->string());
        $this->addColumn('{{%user_client_order}}', 'places_count', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_order}}', 'pvz_code');
        $this->dropColumn('{{%user_client_order}}', 'pvz_info');
        $this->dropColumn('{{%user_client_order}}', 'track_number');
        $this->dropColumn('{{%user_client_order}}', 'places_count');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_110121_add_some_fields_to_user_client_order cannot be reverted.\n";

        return false;
    }
    */
}
