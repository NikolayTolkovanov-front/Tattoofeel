<?php

use yii\db\Migration;

/**
 * Class m200302_091510_product_filters
 */
class m200302_091510_product_filters extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_filters_category}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(128),
            'title' => $this->string(256)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        $this->createTable('{{%product_filters}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(256),
            'title' => $this->string(256)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'category_id' => $this->integer(),
            'sort' => $this->integer()->notNull()->defaultValue(500),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('{{%product_filters_category_product_category}}', [
            'id' => $this->primaryKey(),
            'product_category_id' => $this->integer(),
            'product_filters_category_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_product_filters__category', '{{%product_filters}}', 'category_id', '{{%product_filters_category}}', 'id', 'set null', 'cascade');
        $this->addForeignKey('fk_pfc_pc__product_category', '{{%product_filters_category_product_category}}', 'product_category_id', '{{%product_category}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_pfc_pc__product_filters_category', '{{%product_filters_category_product_category}}', 'product_filters_category_id', '{{%product_filters_category}}', 'id', 'cascade', 'cascade');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_pfc_pc__product_category', '{{%product_filters_category__product_category}}');
        $this->dropForeignKey('fk_pfc_pc__product_filters_category', '{{%product_filters_category__product_category}}');
        $this->dropForeignKey('fk_product_filters__category', '{{%product_filters_category}}');

        $this->dropTable('{{%product_filters__product}}');
        $this->dropTable('{{%product_filters_category}}');
        $this->dropTable('{{%product_filters}}');

        return false;
    }

}
