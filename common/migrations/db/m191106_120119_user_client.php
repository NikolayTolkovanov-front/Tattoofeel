<?php

use yii\db\Migration;

/**
 * Class m191106_120119_user_client
 */
class m191106_120119_user_client extends Migration
{
    /**
     * @return bool|void
     */
    public function safeUp()
    {
        $this->createTable('{{%user_client}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(32),
            'auth_key' => $this->string(32)->notNull(),
            'access_token' => $this->string(40),
            'password_hash' => $this->string()->notNull(),
            'oauth_client' => $this->string(),
            'oauth_client_user_id' => $this->string(),
            'email' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(\common\models\UserClient::STATUS_ACTIVE),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'client_created_at' => $this->integer(),
            'client_updated_at' => $this->integer(),
            'client_created_by' => $this->integer(),
            'client_updated_by' => $this->integer(),
            'logged_at' => $this->integer(),
            'logged_ip' => $this->string(1024),
            'logged_agent' => $this->string(1024),
        ]);

        $this->createTable('{{%user_client_profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'full_name' => $this->string(),
            'avatar_path' => $this->string(),
            'phone' => $this->string(),
            'phone_1' => $this->string(),
            'address_delivery' => $this->string(),
            'link_vk' => $this->string(),
            'link_inst' => $this->string(),
            'sale_ms_id' => $this->string(),
            'client_ms_id' => $this->string(),
            'sale_change' => $this->tinyInteger(2),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'client_created_at' => $this->integer(),
            'client_updated_at' => $this->integer(),
            'client_created_by' => $this->integer(),
            'client_updated_by' => $this->integer(),
        ]);

        $this->createTable('{{%user_client_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->string()->notNull(),
            'token' => $this->string(40)->notNull(),
            'expire_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'client_created_at' => $this->integer(),
            'client_updated_at' => $this->integer(),
            'client_created_by' => $this->integer(),
            'client_updated_by' => $this->integer(),
        ]);
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_client_profile}}');
        $this->dropTable('{{%user_client}}');
        $this->dropTable('{{%user_client_token}}');

    }
}
