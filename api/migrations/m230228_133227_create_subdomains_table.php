<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subdomains}}`.
 */
class m230228_133227_create_subdomains_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subdomains}}', [
            'id' => $this->primaryKey(),
            'subdomain' => $this->string()->notNull(),
            'city' => $this->string(),
            'word_form' => $this->string(),
            'address' => $this->text(),
            'phone' => $this->string(),
            'work_time' => $this->string(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex('idx_subdomains_subdomain', '{{%subdomains}}', 'subdomain', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_subdomains_subdomain', '{{%subdomains}}');
        $this->dropTable('{{%subdomains}}');
    }
}