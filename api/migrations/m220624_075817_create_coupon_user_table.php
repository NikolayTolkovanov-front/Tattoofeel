<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%coupon_user}}`.
 */
class m220624_075817_create_coupon_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%coupon_user}}', [
            'id' => $this->primaryKey(),
            'coupon_id' => $this->integer()->notNull(),
            'user_client_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_coupon_user_coupons',
            '{{%coupon_user}}',
            'coupon_id',
            '{{%coupons}}',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey('fk_coupon_user_user',
            '{{%coupon_user}}',
            'user_client_id',
            '{{%user_client}}',
            'id',
            'cascade',
            'cascade'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_coupon_user_user','{{%coupon_user}}');
        $this->dropForeignKey('fk_coupon_user_coupons','{{%coupon_user}}');
        $this->dropTable('{{%coupon_user}}');
    }
}
