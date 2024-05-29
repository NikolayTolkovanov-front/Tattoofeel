<?php

use yii\db\Migration;

/**
 * Class m201013_132142_add_delivery_settings_table
 */
class m201013_132142_add_delivery_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%delivery_settings}}', [
            'id' => $this->primaryKey(),
            'label' => $this->string()->comment('Наименование'),
            'key' => $this->string()->notNull()->comment('Ключ'),
            'value' => $this->text()->comment('Значение'),
            'description' => $this->text()->comment('Описание'),
        ]);

        $this->insert('{{%delivery_settings}}', [
            'label' => 'Ключ АПИ сессии PickPoint',
            'key' => 'pick_point_session_id',
        ]);
        $this->insert('{{%delivery_settings}}', [
            'label' => 'Дата окончания ключа АПИ сессии PickPoint',
            'key' => 'pick_point_session_id_end_date',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable('{{%delivery_settings}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201013_132142_add_delivery_settings_table cannot be reverted.\n";

        return false;
    }
    */
}
