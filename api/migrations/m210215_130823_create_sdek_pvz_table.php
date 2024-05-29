<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sdek_pvz}}`.
 */
class m210215_130823_create_sdek_pvz_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sdek_pvz}}', [
            'id' => $this->primaryKey(),
            'pvz_code' => $this->string(10)->notNull(),
            'sdek_id' => $this->integer()->notNull(),
            'xml' => $this->text()->notNull(),
        ]);

        $this->createIndex(
            'idx_sdek_pvz_pvz_code',
            '{{%sdek_pvz}}',
            'pvz_code'
        );

        $this->createIndex(
            'idx_sdek_pvz_sdek_id',
            '{{%sdek_pvz}}',
            'sdek_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx_sdek_pvz_sdek_id',
            '{{%sdek_pvz}}'
        );

        $this->dropIndex(
            'idx_sdek_pvz_pvz_code',
            '{{%sdek_pvz}}'
        );

        $this->dropTable('{{%sdek_pvz}}');
    }
}
