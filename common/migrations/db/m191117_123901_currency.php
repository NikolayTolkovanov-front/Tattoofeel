<?php

use yii\db\Migration;

/**
 * Class m191117_123901_currency
 */
class m191117_123901_currency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%currency}}', [
            'id' => $this->primaryKey(),
            'code_iso' => $this->string(10),
            'value' => $this->double(),
            'error' => $this->text(),
            'fullName' => $this->string(128),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'disable_sync' => $this->tinyInteger()->notNull()->defaultValue(0),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0)
        ]);

        $this->createIndex('idx_currency_iso_code', '{{%currency}}', 'code_iso', true);

        $this->addForeignKey('fk_product_currency',
            '{{%product}}',
            'display_currency',
            '{{%currency}}',
            'id',
            'set null',
            'cascade');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_currency_iso_code', '{{%currency}}');
        $this->dropForeignKey('fk_product_currency', '{{%product}}');
        $this->dropTable('{{%currency}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191117_123901_currency cannot be reverted.\n";

        return false;
    }
    */
}
