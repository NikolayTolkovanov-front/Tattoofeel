<?php

use yii\db\Migration;

/**
 * Class m231018_085653_add_roistat
 */
class m231018_085653_add_roistat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%roistat}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(255),
            'host' => $this->string(255)->defaultValue('https://cloud.roistat.com/api/proxy/1.0/leads/add?'),
            'title' => $this->string(255)->defaultValue('Заявка с tattoofeel.ru'),
            'visit' => $this->string(255),
            'name' => $this->string(255),
            'phone' => $this->string(255),
            'email' => $this->string(255),
            'comment' => $this->text(),
            'fields' => $this->text(),
            'isSkipSending' => $this->string(16)->defaultValue('1'),
            'form' => $this->string(255),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231018_085653_add_roistat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231018_085653_add_roistat cannot be reverted.\n";

        return false;
    }
    */
}
