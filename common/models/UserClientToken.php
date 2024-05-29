<?php

namespace common\models;

use common\models\query\UserClientTokenQuery;
use Yii;
use yii\base\InvalidCallException;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_client_token}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $token
 * @property integer $expire_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property UserClient $user
 */
class UserClientToken extends ActiveRecord
{
    use \common\models\traits\BlameAble;

    const TYPE_ACTIVATION = 'activation';
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_LOGIN_PASS = 'login_pass';
    const TOKEN_LENGTH = 40;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_client_token}}';
    }

    /**
     * @return UserClientTokenQuery
     */
    public static function find()
    {
        return new UserClientTokenQuery(get_called_class());
    }

    /**
     * @param mixed $user_id
     * @param string $type
     * @param int|null $duration
     * @return bool|UserClientToken
     * @throws \yii\base\Exception
     */
    public static function create($user_id, $type, $duration = null)
    {
        $model = new self;
        $model->setAttributes([
            'user_id' => $user_id,
            'type' => $type,
            'token' => Yii::$app->security->generateRandomString(self::TOKEN_LENGTH),
            'expire_at' => $duration ? time() + $duration : null
        ]);

        if (!$model->save()) {
            throw new InvalidCallException;
        };

        return $model;

    }

    /**
     * @param $token
     * @param $type
     * @return bool|UserClient
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function use($token, $type)
    {
        $model = self::find()
            ->where(['token' => $token])
            ->andWhere(['type' => $type])
            ->andWhere(['>', 'expire_at', time()])
            ->one();

        if ($model === null) {
            return null;
        }

        $user = $model->user;
        $model->delete();

        return $user;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return $this->ts_behavior();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'token'], 'required'],
            [['user_id', 'expire_at'], 'integer'],
            [['type'], 'string', 'max' => 255],
            [['token'], 'string', 'max' => self::TOKEN_LENGTH]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'type' => Yii::t('common', 'Type'),
            'token' => Yii::t('common', 'Token'),
            'expire_at' => Yii::t('common', 'Expire At'),
            'created_at' => t_b( 'Создан (админ)'),
            'updated_at' => t_b( 'Обновлен (админ)'),
            'created_by' => t_b('Создал (админ)'),
            'updated_by' => t_b('Обновил (админ)'),
            'client_created_at' => t_b( 'Создан (клиент)'),
            'client_updated_at' => t_b( 'Обновлен (клиент)'),
            'client_created_by' => t_b('Создал (клиент)'),
            'client_updated_by' => t_b('Обновил (клиент)'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserClient::class, ['id' => 'user_id']);
    }

    /**
     * @param int|null $duration
     */
    public function renew($duration)
    {
        $this->updateAttributes([
            'expire_at' => $duration ? time() + $duration : null
        ]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->token;
    }
}
