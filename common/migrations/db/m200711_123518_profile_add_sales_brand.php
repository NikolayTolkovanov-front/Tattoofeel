<?php

use yii\db\Migration;

/**
 * Class m200711_123518_profile_add_sales_brand
 */
class m200711_123518_profile_add_sales_brand extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_profile}}', 'sale_brands', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_client_profile}}', 'sale_brands');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200711_123518_profile_add_sales_brand cannot be reverted.\n";

        return false;
    }
    */
}
