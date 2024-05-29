<?php

use yii\db\Migration;

/**
 * Class m200520_131914_add_status_ms_sync_order
 */
class m200520_131914_add_status_ms_sync_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('{{%user_client_order}}');
        if (isset($table->columns['status_ms_sync'])) {
            $this->dropColumn('{{%user_client_order}}', 'status_ms_sync');
        }
        $this->addColumn('{{%user_client_order}}', 'status_ms_sync', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200520_131914_add_status_ms_sync_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_131914_add_status_ms_sync_order cannot be reverted.\n";

        return false;
    }
    */
}
