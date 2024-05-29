<?php

use yii\db\Migration;

/**
 * Class m200524_135511_related_product
 */
class m200524_135511_related_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_related}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'related_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_product_related', '{{%product_related}}', 'product_id', '{{%product}}', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_product_related', '{{%product_related}}');
        $this->dropTable('{{%product_related}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200524_135511_related_product cannot be reverted.\n";

        return false;
    }
    */
}
