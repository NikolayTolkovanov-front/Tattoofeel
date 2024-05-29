<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%delivery_city}}`.
 */
class m210121_182951_create_delivery_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%delivery_city}}', [
            'id' => $this->primaryKey(),
            'sdek_id' => $this->integer()->notNull(),
            'ms_id' => $this->string(),
            'city' => $this->string()->notNull(),
            'city_full' => $this->string(),
            'area' => $this->string(),
            'region' => $this->string(),
            'country' => $this->string()->notNull(),
            'fias_id' => $this->string(),
            'pvz_code' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%delivery_city}}');
    }
}
