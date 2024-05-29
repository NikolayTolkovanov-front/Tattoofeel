<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%delivery_types}}`.
 */
class m201221_082721_create_delivery_types_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'delivery_type', $this->integer());

        $this->createTable('{{%delivery_types}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'ms_title' => $this->string(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_user_client_order_delivery_types',
            '{{%user_client_order}}',
            'delivery_type',
            '{{%delivery_types}}',
            'id',
            'set null',
            'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_client_order_delivery_types','{{%user_client_order}}');
        $this->dropTable('{{%delivery_types}}');
        $this->dropColumn('{{%user_client_order}}', 'delivery_type');
    }
}
