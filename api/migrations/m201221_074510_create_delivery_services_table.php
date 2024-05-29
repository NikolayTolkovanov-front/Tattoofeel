<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%delivery_services}}`.
 */
class m201221_074510_create_delivery_services_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'delivery_service_id', $this->integer());

        $this->createTable('{{%delivery_services}}', [
            'id' => $this->primaryKey(),
            'ms_id' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'code' => $this->string()->notNull(),
            'ms_title' => $this->string(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_user_client_order_delivery_services',
            '{{%user_client_order}}',
            'delivery_service_id',
            '{{%delivery_services}}',
            'id',
            'set null',
            'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_client_order_delivery_services','{{%user_client_order}}');
        $this->dropTable('{{%delivery_services}}');
        $this->dropColumn('{{%user_client_order}}', 'delivery_service_id');
    }
}
