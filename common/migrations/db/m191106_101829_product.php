<?php

use yii\db\Migration;

/**
 * Class m191106_101829_product
 */
class m191106_101829_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product_category}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(128),
            'title' => $this->string(256)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'ms_id' => $this->string(40)->notNull()->unique(),
            'thumbnail_path' => $this->string(128),
            'icon_path' => $this->string(128),
            'body_short' => $this->string(256),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'error' => $this->text(),
            'disable_sync' => $this->tinyInteger()->notNull()->defaultValue(0),
            'small_amount' => $this->integer(),
            'large_amount' => $this->integer(),
            'avr_amount' => $this->integer(),
        ]);

        $this->createTable('{{%product_category_config}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(128)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'ms_id' => $this->string(40)->notNull()->unique(),
            'error' => $this->text(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'disable_sync' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(256),
            'title' => $this->string(256)->notNull(),
            'title_short' => $this->string(24),
            'body' => $this->text(),
            'body_short' => $this->string(256),
            'thumbnail_path' => $this->string(128),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'revised' => $this->tinyInteger()->notNull()->defaultValue(0),
            'disable_sync' => $this->tinyInteger()->notNull()->defaultValue(0),
            'disable_sync_prop' => $this->text(),
            'is_main_in_config' => $this->tinyInteger()->notNull()->defaultValue(0),

            'category_ms_id' => $this->string(40),
            'config_ms_id' => $this->string(40),

            'published_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'ms_id' => $this->string(40)->notNull()->unique(),
            'manufacturer' => $this->string(128),
            'display_currency' => $this->integer(),
            'sale' => $this->integer(),
            'brand_id' => $this->integer(),
            'weight' => $this->float(),
            'amount' => $this->smallInteger(),
            'min_amount' => $this->smallInteger(),
            'order' => $this->integer(),
            'error' => $this->text(),
            'type_eq' => $this->integer(),
            'article' => $this->string(40),
        ]);

        $this->createTable('{{%product_attachment}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'path' => $this->string(128)->notNull(),
            'type' => $this->string(32),
            'size' => $this->integer(),
            'name' => $this->string(128),
            'created_at' => $this->integer(),
            'order' => $this->tinyInteger()
        ]);

        $this->addForeignKey('fk_product_attachment_product', '{{%product_attachment}}', 'product_id', '{{%product}}', 'id', 'cascade', 'cascade');

        $this->createIndex('idx_product_title', '{{%product}}', 'title', false);
        $this->createIndex('idx_product_article', '{{%product}}', 'article', false);
        $this->createIndex('idx_product_title_short', '{{%product}}', 'title_short', false);
        $this->createIndex('idx_product_slug', '{{%product}}', 'slug', false);
        $this->createIndex('idx_product_ms_id', '{{%product}}', 'ms_id', true);
        $this->createIndex('idx_product_manufacturer', '{{%product}}', 'manufacturer', false);
        $this->createIndex('idx_product_category_body_short', '{{%product_category}}', 'body_short', false);
        $this->createIndex('idx_product_category_slug', '{{%product_category}}', 'slug', true);
        $this->createIndex('idx_product_category_title', '{{%product_category}}', 'title', false);
        $this->createIndex('idx_product_category_config_title', '{{%product_category_config}}', 'title', true);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_product_title', '{{%product}}');
        $this->dropIndex('idx_product_article', '{{%product}}');
        $this->dropIndex('idx_product_title_short', '{{%product}}');
        $this->dropIndex('idx_product_slug', '{{%product}}');
        $this->dropIndex('idx_product_ms_id', '{{%product}}');
        $this->dropIndex('idx_product_manufacturer', '{{%product}}');
        $this->dropIndex('idx_product_category_body_short', '{{%product_category}}');
        $this->dropIndex('idx_product_category_slug', '{{%product_category}}');
        $this->dropIndex('idx_product_category_title', '{{%product_category}}');
        $this->dropIndex('idx_product_category_config_title', '{{%product_category_config}}');

        $this->dropForeignKey('fk_product_attachment_product', '{{%product_attachment}}');

        $this->dropTable('{{%product_attachment}}');
        $this->dropTable('{{%product}}');
        $this->dropTable('{{%product_category}}');
        $this->dropTable('{{%product_category_config}}');
    }
}
