<?php

use yii\db\Migration;

/**
 * Class m240114_221417_update_filters_junction_table
 */
class m240114_221417_update_filters_junction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_filters_category_product_category}}', 'visible_in_menu',
            $this->boolean()->defaultValue(false)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240114_221417_update_filters_junction_table cannot be reverted.\n";

        return false;
    }
}
