<?php

namespace frontend\models;

use yii\base\Model;

class Roistat extends Model
{
    private $key = null;
    private $host = 'https://cloud.roistat.com/api/proxy/1.0/leads/add?';
    private $title = 'Заявка с tattoofeel.ru';
    private $visit = null;
    private $name = null;
    private $phone = null;
    private $email = null;
    private $comment = null;
    private $fields = [];
    private $isSkipSending = '1';
    private $form = null;

    public function __construct($config = [])
    {
        $this->key = env('RS_KEY');
        parent::__construct($config);
    }

    /**
     * Функция проверки наличия телефона или email
     * @return bool
     */
    public function execute(): bool
    {
        if (empty($this->getPhone()) && empty($this->getEmail())) return false;
        return true;
    }

    /**
     * Функция обработчика форм:
     * 1. Получает модель класса $model с данными
     * 2. Получает список атрибутов модели
     * 3. Соотносит его со своими свойствами
     * 4. Проверяет наличие телефона или email
     * 5. Отправляет проксилид в Roistat
     */
    public function handlerForm($model, $roistatFormField) {
        $attr = $model->attributeLabels();
        if(!empty($roistatFormField)){
            $this->setFields([
                'form' => $roistatFormField,
            ]);
        }
        foreach ($attr as $key => $value) {
            switch ($key) {
                case 'name':
                    $this->setName($model->name);
                    break;
                case 'email':
                    $this->setEmail($model->email);
                    break;
                case 'phone':
                    $this->setPhone($model->phone);
                    break;
                case 'body':
                    $this->setComment($model->body);
                    break;
//                default:
//                    $this->setFields($this->getFields() + [$key -> $model->$key]);
            }
        }

        $this->setVisit(isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie');

        if ($this->execute()) {
            $this->sendProxyLead();
        }
    }

    /**
     * Функция получения параметров для отправки проксилида
     * @return array
     */
    public function roistatData(): array
    {
        $roistatData = array(
            'roistat'         => $this->getVisit(),
            'key'             => $this->getKey(),
            'title'           => $this->getTitle(),
            'name'            => $this->getName(),
            'phone'           => $this->getPhone(),
            'email'           => $this->getEmail(),
            'comment'         => $this->getComment(),
            'is_skip_sending' => $this->getIsSkipSending(),
            'fields'          => $this->getFields()
        );

         return $roistatData;
    }

    /**
     * Функция отправки проксилида
     */
    public function sendProxyLead(): void
    {
        $this->setVisit(isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie');
        file_get_contents($this->getHost() . http_build_query($this->roistatData()));
    }

    /**
     * @return string
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getVisit(): ?string
    {
        return $this->visit;
    }

    /**
     * @param string $visit
     */
    public function setVisit(string $visit): void
    {
        $this->visit = $visit;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIsSkipSending(): ?string
    {
        return $this->isSkipSending;
    }

    /**
     * @param string $isSkipSending
     */
    public function setIsSkipSending(string $isSkipSending): void
    {
        $this->isSkipSending = $isSkipSending;
    }

    /**
     * @return string
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getForm(): ?string
    {
        return $this->form;
    }

    /**
     * @param string $form
     */
    public function setForm(string $form): void
    {
        $this->form = $form;
    }

    /**
     * @return array
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
}
