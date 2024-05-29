<?php

namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\UserClientQuery;
use vova07\console\ConsoleRunner;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 *@property  UserClientProfile $userProfile
 *@property  UserClientProfile $profile
 */

class UserClient extends ActiveRecord implements IdentityInterface
{
    use \common\models\traits\BlameAble;

    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;

    const ORDER_STATUS_COMPLETED = 22;
    const ORDER_STATUS_COMPLETED_PASS = 23;
    const ORDER_STATUS_REFUSE = 24;

    const EVENT_AFTER_SIGNUP = 'afterSignup';
    const EVENT_AFTER_LOGIN = 'afterLogin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_client}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->cache(7200)
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @return UserClientQuery
     */
    public static function find()
    {
        return new UserClientQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //return null;
        return static::find()
            ->cache(600)
            ->active()
            ->andWhere(['auth_key' => $token])
            ->one();
    }

    /**
     * Finds userClient by username
     *
     * @param string $username
     * @return UserClient|array|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->cache(7200)
            ->active()
            ->andWhere(['username' => $username])
            ->one();
    }

    /**
     * Finds userClient by username or email
     *
     * @param string $login
     * @return UserClient|array|null
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->cache(7200)
            ->active()
            ->andWhere(['or', ['username' => $login], ['email' => $login]])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCoupons() {
        return $this->hasMany(Coupons::class, ['id' => 'coupon_id'])
            ->viaTable('{{%coupon_user}}', ['user_client_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            $this->ts_behavior(),
            [
                'auth_key' => [
                    'class' => AttributeBehavior::class,
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                    ],
                    'value' => Yii::$app->getSecurity()->generateRandomString()
                ]
            ]
        );
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create' => [
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['username'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
            [['is_manager'], 'default', 'value' => 0],
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_NOT_ACTIVE => t_b('Не активный'),
            self::STATUS_ACTIVE => t_b( 'Активный'),
            self::STATUS_DELETED => t_b('Заблокированный')
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => t_b( 'Ид.'),
            'username' => t_b( 'Логин'),
            'email' => t_b( 'Email'),
            'status' => t_b( 'Статус'),
            'logged_at' => t_b( 'Последний логин'),
            'created_at' => t_b( 'Создан (админ)'),
            'updated_at' => t_b( 'Обновлен (админ)'),
            'created_by' => t_b('Создал (админ)'),
            'updated_by' => t_b('Обновил (админ)'),
            'client_created_at' => t_b( 'Создан (клиент)'),
            'client_updated_at' => t_b( 'Обновлен (клиент)'),
            'client_created_by' => t_b('Создал (клиент)'),
            'client_updated_by' => t_b('Обновил (клиент)'),
            'is_manager' => t_b('Менеджер'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserClientProfile::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->userProfile;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current userClient
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Creates userClient profile and application event
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [])
    {
        $this->refresh();

        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event' => 'signup',
            'data' => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id' => $this->getId(),
                'created_at' => $this->created_at
            ]
        ]));

        $profile = new UserClientProfile();
        $profile->load($profileData, '');
        $profile->user_id = $this->id;
        $profile->save(false);
        $this->link('userProfile', $profile);
        $this->trigger(self::EVENT_AFTER_SIGNUP);

        $this->syncProfileFork();
    }

    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullname()) {
            return $this->userProfile->getFullname();
        }
        if ($this->username) {
            return $this->username;
        }
        return $this->email;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function afterLogin() {
        $this->trigger(self::EVENT_AFTER_LOGIN);
        $this->syncProfileFork();
    }

//    //cart
//    public function getOrders()
//    {
//        return $this->hasMany(UserClientOrder::class, ['user_id' => 'id'])
//            ->where(['isCart' => 0])
//            ->orderBy(['id' => SORT_DESC]);
//    }

    //cart
    public function getOrders()
    {
        return $this->hasMany(UserClientOrder::class, ['user_id' => 'id'])
            ->where(['isCart' => 0])
            ->andWhere(['not', ['status' => null]])
            ->andWhere(['>', 'sum_buy', 0])
            ->orderBy(['date' => SORT_DESC]);
    }

    public function getOpenOrders()
    {
        return $this->hasMany(UserClientOrder::class, ['user_id' => 'id'])
            ->where(['isCart' => 0])
            ->andWhere(['not', ['status' => null]])
            ->andWhere(['not', ['status' => self::ORDER_STATUS_COMPLETED]])
            ->andWhere(['not', ['status' => self::ORDER_STATUS_COMPLETED_PASS]])
            ->andWhere(['not', ['status' => self::ORDER_STATUS_REFUSE]])
            ->andWhere(['>', 'sum_buy', 0]);
    }

    public function getSale() {
        return 'Нет скидки';
    }

    public function getOrdersCount() {
        return count($this->orders);
    }

    public function getOrdersSum()
    {
        $sum = 0;
        foreach ($this->orders as $order)
            //$sum += $order->sum;
            $sum += $order->sum_buy + $order->sum_delivery;

        return $sum;
    }

    public function getOrdersSumFormatted() {
        $price = ProductPrice::getParsePrice(
            $this->ordersSum,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );
        return $this->ordersSum ? implode('',[ $price->ceil_fr, ' ', $price->cur ]) : 0;
    }

    protected function syncProfileFork() {
        if (!empty($p = $this->userProfile)) {
            $cr = new ConsoleRunner(['file' => '@console/yii', 'phpBinaryPath' => 'php']);
            $cr->run("sync/sync-client" ." $p->id");
        }

    }
}
