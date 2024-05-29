<?php

use yii\db\Migration;

/**
 * Class m200226_050711_type_eq
 */
class m200226_050711_type_eq extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%type_eq}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(128),
            'title' => $this->string(256)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_product_type_eq',
            '{{%product}}',
            'type_eq',
            '{{%type_eq}}',
            'id',
            'set null',
            'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_product_type_eq','{{%product}}');
        $this->dropTable('{{%type_eq}}');
    }
}
