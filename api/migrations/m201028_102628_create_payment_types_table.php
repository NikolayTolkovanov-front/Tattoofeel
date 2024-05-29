<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payment_types}}`.
 */
class m201028_102628_create_payment_types_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'payment_type', $this->integer());

        $this->createTable('{{%payment_types}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(256)->notNull(),
            'logo_path' => $this->string(256),
            'desc' => $this->text(),
            'active' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_user_client_order_payment_types',
            '{{%user_client_order}}',
            'payment_type',
            '{{%payment_types}}',
            'id',
            'set null',
            'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_client_order_payment_types','{{%user_client_order}}');
        $this->dropTable('{{%payment_types}}');
        $this->dropColumn('{{%user_client_order}}', 'payment_type');
    }
}
