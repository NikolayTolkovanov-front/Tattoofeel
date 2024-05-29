<?php

use yii\db\Migration;

/**
 * Class m200302_191740_question
 */
class m200302_191740_question extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%question}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(512),
            'title' => $this->string(512)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'answer' => $this->text(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('{{%question_product}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'question_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_qp__product', '{{%question_product}}', 'product_id', '{{%product}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_qp__question', '{{%question_product}}', 'question_id', '{{%question}}', 'id', 'cascade', 'cascade');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_qp__product', '{{%question_product}}');
        $this->dropForeignKey('fk_qp__question', '{{%question_product}}');
        $this->dropTable('{{%question}}');
        $this->dropTable('{{%question_product}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200302_191740_question cannot be reverted.\n";

        return false;
    }
    */
}
