<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%iml_city}}`.
 */
class m201015_153310_create_iml_city_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%iml_city}}', [
            'id' => $this->primaryKey(),
            'city' => $this->string(),
            'region' => $this->string(),
            'area' => $this->string()->comment('Район'),
            'region_iml' => $this->string(),
            'rate_zone_moscow' => $this->string(),
            'rate_zone_spb' => $this->string(),
            'fias' => $this->string()->comment('код ФИАС')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%iml_city}}');
    }
}
