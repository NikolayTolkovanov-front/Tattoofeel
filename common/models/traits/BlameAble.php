<?php

namespace common\models\traits;

use common\components\BlameableBehavior;
use common\components\TimestampBehavior;
use common\models\User;
use common\models\UserClient;
use Yii;

trait BlameAble
{

    public function ts_behavior() {

        $isClient = isset(Yii::$app->client);

        if ($isClient)
            return [
                'timestamp' => [
                    'class' => TimestampBehavior::class,
                    'createdAtAttribute' => 'client_created_at',
                    'updatedAtAttribute' => 'client_updated_at',
                ],
                'blameable' => [
                    'class' => BlameableBehavior::class,
                    'createdByAttribute' => 'client_created_by',
                    'updatedByAttribute' => 'client_updated_by',
                ]
            ];

        return [
            'timestamp' => TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
        ];
    }

    protected function getUserConsole() {
        return (object) ['id'=>'-1','username'=>t_b('система_console')];
    }

    protected function getUserNotFound() {
        return (object) ['id'=>'-2','username'=>t_b('система_ntf')];
    }

    /**
     * @return \yii\db\ActiveQuery|object
     * @throws
     */
    public function getAuthor()
    {

        $isClient = isset(Yii::$app->client);

        $userClass = $isClient ?
            UserClient::class : User::class;

        if ($this->created_by == -1)
            return $this->getUserConsole();

        return $this->hasOne($userClass, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery|object
     * @throws
     */
    public function getUpdater()
    {
        $isClient = isset(Yii::$app->client);

        $userClass = $isClient ?
            UserClient::class : User::class;

        if ($this->updated_by == -1)
            return $this->getUserConsole();

        return $this->hasOne($userClass, ['id' => 'updated_by']);
    }


    /**
     * @return \yii\db\ActiveQuery|object
     * @throws
     */
    public function getAuthorClient()
    {
        return $this->hasOne(UserClient::class, ['id' => 'client_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery|object
     * @throws
     */
    public function getUpdaterClient()
    {
        return $this->hasOne(UserClient::class, ['id' => 'client_updated_by']);
    }


    /**
     * @return \yii\db\ActiveQuery|object
     * @throws
     */
    public function getSender()
    {
        $userClass = User::class;

        if (is_null($this->author))
            return $this->getUserNotFound();

        if ($this->author == -1)
            return (object) ['id'=>'-1','username'=>t_b('система_a')];

        return $this->hasOne($userClass, ['id' => 'author']);
    }
}
