<?php

use yii\db\Migration;

/**
 * Class m191201_131607_product_price
 */
class m191201_131607_product_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('{{%product_price_template}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(128)->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('{{%product_price}}', [
            'id' => $this->primaryKey(),
            'template_id' => $this->integer(),
            'price' => $this->double(),
            'currency_isoCode' => $this->string(8),
            'product_id' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('fk_product_price__product', '{{%product_price}}', 'product_id','{{%product}}', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk_product_price__template', '{{%product_price}}', 'template_id','{{%product_price_template}}', 'id', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropForeignKey('fk_product_price__product', '{{%product_price}}');
        $this->dropForeignKey('fk_product_price__template', '{{%product_price}}');
        $this->dropTable('{{%product_price}}');
    }
}
