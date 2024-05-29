<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%reviews}}`.
 */
class m220909_141333_create_reviews_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%reviews}}', [
            'id' => $this->primaryKey(),
            'user_client_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'is_published' => $this->tinyInteger()->notNull()->defaultValue(0),
            'rating' => $this->double()->notNull()->defaultValue(0),
            'date' => $this->integer(),
            'text' => $this->text(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_reviews_user_client',
            '{{%reviews}}',
            'user_client_id',
            '{{%user_client}}',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey('fk_reviews_product',
            '{{%reviews}}',
            'product_id',
            '{{%product}}',
            'id',
            'cascade',
            'cascade'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_reviews_product','{{%reviews}}');
        $this->dropForeignKey('fk_reviews_user_client','{{%reviews}}');
        $this->dropTable('{{%reviews}}');
    }
}
