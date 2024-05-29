<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bank_cards}}`.
 */
class m210316_110943_create_bank_cards_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bank_cards}}', [
            'id' => $this->primaryKey(),
            'sort' => $this->integer()->notNull(),
            'number' => $this->string()->notNull(),
            'owner' => $this->string()->notNull(),
            'text' => $this->text(),
            'is_actual' => $this->tinyInteger()->notNull()->defaultValue(0),
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
        $this->dropTable('{{%bank_cards}}');
    }
}
