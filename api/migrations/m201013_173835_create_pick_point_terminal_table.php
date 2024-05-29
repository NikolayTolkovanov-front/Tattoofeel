<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pick_point_terminal}}`.
 */
class m201013_173835_create_pick_point_terminal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%pick_point_terminal}}', [
            'id' => $this->primaryKey(),
            'terminal_id' => $this->integer()->comment('Id постамата'),
            'address' =>$this->text(),
            'card' =>$this->smallInteger()->comment('Возможность оплаты пластиковой картой: 0 – нет, 1 –да, 2 – только онлайн оплата'),
            'cash' =>$this->smallInteger()->comment('Возможность оплаты наличными: 0 – нет, 1 – да'),
            'city_id' => $this->integer()->comment('Id города'),
            'city_name' => $this->string()->comment('Название города'),
            'country_iso' =>$this->string()->comment('ISO код страны'),
            'country_name' => $this->string(),
            'file'=>$this->text()->comment('Фото. Адрес фотки подставляется к основному url АПИ '),
            'house' => $this->string(),
            'in_description' => $this->text()->comment('Полное описание местонахождения терминала внутри'),
            'out_description' => $this->text()->comment('Полное описание местонахождения терминала снаружи'),
            'latitude'=> $this->float(),
            'longitude'=> $this->float(),
            'name' =>$this->text()->comment('название'),
            'number' => $this->string()->comment('номер постамат, (PTNumber) '),
            'opening' => $this->smallInteger(),
            'owner_id' => $this->integer(),
            'owner_name' => $this->string()->comment('название сети постаматов'),
            'post_code' => $this->string()->comment('почтовый индекс'),
            'region' => $this->string(),
            'status' => $this->integer()->comment('Статус постамата:  2 – рабочий, 5 - перегружен'),
            'street' => $this->string(),
            'temporarily_closed' => $this->smallInteger(),
            'type_title' => $this->string(),
            'work_time' => $this->text(),
            'work_time_sms' => $this->text(),
            'work_hourly' => $this->smallInteger()->comment('<1/0 – работает круглосуточно'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pick_point_terminal}}');
    }
}
