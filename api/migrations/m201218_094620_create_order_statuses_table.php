<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_statuses}}`.
 */
class m201218_094620_create_order_statuses_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'status', $this->integer());

        $this->createTable('{{%order_statuses}}', [
            'id' => $this->primaryKey(),
            'ms_status_id' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'ms_title' => $this->string(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_user_client_order_statuses',
            '{{%user_client_order}}',
            'status',
            '{{%order_statuses}}',
            'id',
            'set null',
            'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_client_order_statuses','{{%user_client_order}}');
        $this->dropTable('{{%order_statuses}}');
        $this->dropColumn('{{%user_client_order}}', 'status');
    }
}
