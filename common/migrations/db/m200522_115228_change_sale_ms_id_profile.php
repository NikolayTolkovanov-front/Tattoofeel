<?php

use yii\db\Migration;

/**
 * Class m200522_115228_change_sale_ms_id_profile
 */
class m200522_115228_change_sale_ms_id_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('{{%user_client_profile}}');
        if (isset($table->columns['sale_ms_id'])) {
            $this->dropColumn('{{%user_client_profile}}', 'sale_ms_id');
        }
        $this->addColumn('{{%user_client_profile}}', 'sale_ms_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200522_115228_change_sale_ms_id_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200522_115228_change_sale_ms_id_profile cannot be reverted.\n";

        return false;
    }
    */
}
