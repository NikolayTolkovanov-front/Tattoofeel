<?php

use yii\db\Migration;

/**
 * Class m240105_225834_add_nested_categories
 */
class m240105_225834_add_nested_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_category}}', 'parent_id',
            $this->smallInteger()->unsigned()->after('id')->null()
        );
        $this->createIndex('product_category_parent_id', '{{%product_category}}', 'parent_id');
        $this->addColumn('{{%product_category}}', 'level',
            $this->smallInteger()->unsigned()->after('parent_id')->notNull()->defaultValue(0)
        );
        $this->createIndex('product_category_level', '{{%product_category}}', 'level');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240105_225834_add_nested_categories cannot be reverted.\n";

        return false;
    }
}
