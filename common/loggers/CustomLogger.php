<?php

namespace common\loggers;

use Yii;
use common\models\Product;
use yii\helpers\Url;

/*
 * use common\loggers\CustomLogger;
 *
 * $log = new \common\loggers\CustomLogger();
 * $log->createLogs();
 */

class CustomLogger
{
    protected $filepath = '';
    protected $handle;
    protected $productFile;
    protected $email = array(
        'medvedgreez@yandex.ru',
        'toaster16mb@gmail.com',
    );
    //protected $replyEmail = 'greezkor@gmail.com';

    public function __construct()
    {
        $arDate = explode('_', date('Y-m-d_His'));
        $dirname = dirname(__FILE__) . '/logs/' . $arDate[0];

        $this->filepath = $dirname . '/' . $arDate[0] . '_' . $arDate[1] . '.log';
        $this->productFile = dirname(__FILE__) . '/logs/prod_count.txt';

        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }

        $this->handle = fopen($this->filepath, 'w');
    }

    public function __destruct()
    {
        fclose($this->handle);
    }

    public function createLogs()
    {
        $this->saveProductsData();
        $this->saveInputData();
        $this->saveHeaders();
        $this->saveServerData();
        $this->saveGetData();
        $this->savePostData();
    }

    protected function saveProductsData()
    {
        $count = 0;
        try {
            $pdo = new \PDO(env('DB_DSN'), env('DB_USERNAME'), env('DB_PASSWORD'));

            $query = $pdo->prepare('SELECT COUNT(*) AS cnt FROM `tt_product` WHERE (`tt_product`.`is_ms_deleted` = 1)');
            $query->execute();
            $data = $query->fetch();

            if (isset($data['cnt'])) {
                $count = $data['cnt'];
            }

            $pdo = null;
        } catch (\PDOException $e) {
            fputs($this->handle, 'DB Errors: ' . $e->getMessage() . "\n\n");
        }

        //$count = Product::find()->select('COUNT(*) AS cnt')->asArray()->one();

        $curCount = intval($count);
        fputs($this->handle, 'Products Count (is_ms_deleted=1) = ' . print_r($curCount, true));
        fputs($this->handle, "\n");
        fputs($this->handle, "\n");

        if (!file_exists($this->productFile)) {
            $prevCount = $curCount;
        } else {
            $prevCount = intval(file_get_contents($this->productFile));
        }

        file_put_contents($this->productFile, $curCount);

        if ($prevCount < $curCount) {
            // отправить сообщение на email
            $this->sendMail();
//            die('send');
        }
    }

    protected function sendMail()
    {
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, env('FRONTEND_HOST_INFO') . '/send-notification-to-admin/');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_exec($curl);
            curl_close($curl);
        }

//        $date = date('d.m.Y H:i:s');
//
//        $from = env('ROBOT_EMAIL');
//        $to = implode(', ', $this->email);
//        $subject = "Tattoofeel: были удалены товары из каталога [{$date}]";
//        $message = "Это письмо было отправлено, потому что уменьшилось количество товаров в каталоге Tattoofeel. Время этого события: {$date}";
//        $headers = "From:" . $from;
//
//        return mail($to, $subject, $message, $headers);

//        return Yii::$app->mailer->compose()
//            ->setTo($this->email)
//            //->setTo('medvedgreez@yandex.ru')
//            ->setFrom(env('ROBOT_EMAIL'))
//            ->setReplyTo($this->replyEmail)
//            ->setSubject("Tattoofeel: были удалены товары из каталога [$date]")
//            ->setHtmlBody("<p>Это письмо было отправлено, потому что уменьшилось количество товаров в каталоге Tattoofeel.</p><p>Время этого события: $date</p>")
//            ->send();
    }

    protected function saveHeaders()
    {
        fputs($this->handle, 'Headers = ' . print_r(getallheaders(), true));
        fputs($this->handle, "\n");
    }

    protected function saveInputData()
    {
        fputs($this->handle, 'php://input = ' . print_r(@file_get_contents('php://input'), true));
        fputs($this->handle, "\n");
        fputs($this->handle, "\n");
    }

    protected function saveGetData()
    {
        fputs($this->handle, '$_GET = ' . print_r($_GET, true));
        fputs($this->handle, "\n");
    }

    protected function savePostData()
    {
        fputs($this->handle, '$_POST = ' . print_r($_POST, true));
        fputs($this->handle, "\n");
    }

    protected function saveServerData()
    {
        fputs($this->handle, '$_SERVER = ' . print_r($_SERVER, true));
        fputs($this->handle, "\n");
    }
}