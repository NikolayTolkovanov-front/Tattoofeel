<?php

use yii\db\Migration;

/**
 * Class m211028_160034_add_coupon_id_field_to_user_client_order
 */
class m211028_160034_add_coupon_id_field_to_user_client_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_client_order}}', 'coupon_id', $this->integer());
        $this->addColumn('{{%user_client_order}}', 'sum_discount', $this->double()->after('sum_buy'));
        $this->addColumn('{{%user_client_order}}', 'sum_delivery_discount', $this->double()->after('sum_delivery'));

        $this->addForeignKey('fk_user_client_order_coupons',
            '{{%user_client_order}}',
            'coupon_id',
            '{{%coupons}}',
            'id',
            'set null',
            'cascade'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_client_order_coupons','{{%user_client_order}}');
        $this->dropColumn('{{%user_client_order}}', 'sum_delivery_discount');
        $this->dropColumn('{{%user_client_order}}', 'sum_discount');
        $this->dropColumn('{{%user_client_order}}', 'coupon_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210330_095744_add_commission_field_to_user_client_order cannot be reverted.\n";

        return false;
    }
    */
}
