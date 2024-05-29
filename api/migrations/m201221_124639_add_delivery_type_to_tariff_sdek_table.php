<?php

use yii\db\Migration;

/**
 * Class m201221_124639_add_delivery_type_to_tariff_sdek_table
 */
class m201221_124639_add_delivery_type_to_tariff_sdek_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%tariff_sdek}}', 'delivery_type', $this->integer());

        $this->addForeignKey('fk_tariff_sdek_delivery_type',
            '{{%tariff_sdek}}',
            'delivery_type',
            '{{%delivery_types}}',
            'id',
            'set null',
            'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_tariff_sdek_delivery_type','{{%tariff_sdek}}');
        $this->dropColumn('{{%tariff_sdek}}', 'delivery_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_124639_add_delivery_type_to_tariff_sdek_table cannot be reverted.\n";

        return false;
    }
    */
}
