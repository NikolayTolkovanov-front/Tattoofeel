<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tariff_sdek}}`.
 */
class m201221_095653_create_tariff_sdek_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'tariff_sdek', $this->integer());

        $this->createTable('{{%tariff_sdek}}', [
            'id' => $this->primaryKey(),
            'ms_id' => $this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'sdek_id' => $this->integer(),
            'ms_title' => $this->string(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_user_client_order_tariff_sdek',
            '{{%user_client_order}}',
            'tariff_sdek',
            '{{%tariff_sdek}}',
            'id',
            'set null',
            'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_client_order_tariff_sdek','{{%user_client_order}}');
        $this->dropTable('{{%tariff_sdek}}');
        $this->dropColumn('{{%user_client_order}}', 'tariff_sdek');
    }
}
