<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%commission}}`.
 */
class m210329_101132_create_commission_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%commission}}', [
            'id' => $this->primaryKey(),
            'payment_type_id' => $this->integer()->notNull(),
            'percent' => $this->float()->notNull(),
            'discount_group' => $this->string()->notNull(),
            'text' => $this->text(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%commission}}');
    }
}
