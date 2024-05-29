<?php

use yii\db\Migration;

/**
 * Class m200520_121226_clint_Profile_sale_change
 */
class m200520_121226_clint_Profile_sale_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('{{%user_client_profile}}');
        if (isset($table->columns['sale_change'])) {
            $this->dropColumn('{{%user_client_profile}}', 'sale_change');
        }
        $this->addColumn('{{%user_client_profile}}', 'sale_change', $this->tinyInteger(6));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200520_121226_clint_Profile_sale_change cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200520_121226_clint_Profile_sale_change cannot be reverted.\n";

        return false;
    }
    */
}
