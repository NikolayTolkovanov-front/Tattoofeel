<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%coupons}}`.
 */
class m211028_141212_create_coupons_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%coupons}}', [
            'id' => $this->primaryKey(),
            'coupon_code' => $this->string(191)->notNull(),
            'active_until' => $this->integer(),
            'uses_count' => $this->integer()->notNull()->defaultValue(0),
            'used_count' => $this->integer()->notNull()->defaultValue(0),
            'is_percent' => $this->tinyInteger()->notNull()->defaultValue(0),
            'coupon_value' => $this->double(),
            'order_sum_min' => $this->double()->notNull()->defaultValue(0),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex('idx_coupons_coupon_code', '{{%coupons}}', 'coupon_code', true);

        $this->createTable('{{%coupon_category}}', [
            'id' => $this->primaryKey(),
            'coupon_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_coupon_category_coupons',
            '{{%coupon_category}}',
            'coupon_id',
            '{{%coupons}}',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey('fk_coupon_category_category',
            '{{%coupon_category}}',
            'category_id',
            '{{%product_category}}',
            'id',
            'cascade',
            'cascade'
        );

        $this->createTable('{{%coupon_brand}}', [
            'id' => $this->primaryKey(),
            'coupon_id' => $this->integer()->notNull(),
            'brand_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_coupon_brand_coupons',
            '{{%coupon_brand}}',
            'coupon_id',
            '{{%coupons}}',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey('fk_coupon_brand_brand',
            '{{%coupon_brand}}',
            'brand_id',
            '{{%brand}}',
            'id',
            'cascade',
            'cascade'
        );

        $this->createTable('{{%coupon_product}}', [
            'id' => $this->primaryKey(),
            'coupon_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_coupon_product_coupons',
            '{{%coupon_product}}',
            'coupon_id',
            '{{%coupons}}',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey('fk_coupon_product_product',
            '{{%coupon_product}}',
            'product_id',
            '{{%product}}',
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
        $this->dropForeignKey('fk_coupon_product_product','{{%coupon_product}}');
        $this->dropForeignKey('fk_coupon_product_coupons','{{%coupon_product}}');
        $this->dropTable('{{%coupon_product}}');

        $this->dropForeignKey('fk_coupon_brand_brand','{{%coupon_brand}}');
        $this->dropForeignKey('fk_coupon_brand_coupons','{{%coupon_brand}}');
        $this->dropTable('{{%coupon_brand}}');

        $this->dropForeignKey('fk_coupon_category_category','{{%coupon_category}}');
        $this->dropForeignKey('fk_coupon_category_coupons','{{%coupon_category}}');
        $this->dropTable('{{%coupon_category}}');

        $this->dropIndex('idx_coupons_coupon_code', '{{%coupons}}');
        $this->dropTable('{{%coupons}}');
    }
}
