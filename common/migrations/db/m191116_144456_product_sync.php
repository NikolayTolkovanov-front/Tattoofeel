<?php

use yii\db\Migration;

/**
 * Class m191116_144456_product_sync
 */
class m191116_144456_product_sync extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_sync}}', [
            'id' => $this->primaryKey(),
            'author' => $this->integer(),
            'date' => $this->integer(),
            'products' => 'LONGTEXT',
            'error' => 'LONGTEXT',
            'status' => $this->tinyInteger()->notNull()->defaultValue(1)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%product_sync}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191116_144456_product_sync cannot be reverted.\n";

        return false;
    }
    */
}
