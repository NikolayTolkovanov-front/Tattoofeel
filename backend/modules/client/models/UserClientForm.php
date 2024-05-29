<?php

namespace backend\modules\client\models;

use common\models\UserClient;
use yii\base\Exception;
use yii\base\Model;

/**
 * Create user form
 */
class UserClientForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;
    public $is_manager;

    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => UserClient::class, 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->getModel()->id]]);
                }
            }],
            ['username', 'string', 'min' => 2, 'max' => 32],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => UserClient::class, 'filter' => function ($query) {
                if (!$this->getModel()->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->getModel()->id]]);
                }
            }],

            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],

            [['status'], 'integer'],
            [['status'], 'default', 'value' => 1],

            [['is_manager'], 'integer'],
            [['is_manager'], 'default', 'value' => 0],
        ];
    }

    /**
     * @return UserClient
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new UserClient();
        }
        return $this->model;
    }

    /**
     * @param UserClient $model
     * @return mixed
     */
    public function setModel($model)
    {
        $this->username = $model->username;
        $this->email = $model->email;
        $this->status = $model->status;
        $this->is_manager = $model->is_manager;
        $this->model = $model;
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => t_b( 'Логин'),
            'email' => t_b( 'Email'),
            'status' => t_b( 'Статус'),
            'is_manager' => t_b( 'Менеджер'),
            'password' => t_b( 'Пароль')
        ];
    }

    /**
     * Signs user up.
     * @return UserClient|null the saved model or null if saving fails
     * @throws Exception
     */
    public function save()
    {
        if ($this->validate()) {
            $model = $this->getModel();
            $isNewRecord = $model->getIsNewRecord();
            $model->username = $this->username;
            $model->email = $this->email;
            $model->status = $this->status;
            $model->is_manager = $this->is_manager;

            if ($this->password) {
                $model->setPassword($this->password);
            }
            if (!$model->save()) {
                throw new Exception('Model not saved');
            }
            if ($isNewRecord) {
                $model->afterSignup();
            }

            return !$model->hasErrors();
        }
        return null;
    }
}
