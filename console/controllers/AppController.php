<?php

namespace console\controllers;

use common\models\ProductCategory;
use frontend\models\Product;
use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Html;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class AppController extends Controller
{
    /** @var array */
    public $writablePaths = [
        '@common/runtime',
        '@frontend/runtime',
        '@frontend/web/assets',
        '@backend/runtime',
        '@backend/web/assets',
        '@storage/cache',
        '@storage/web/source',
        '@api/runtime',
    ];

    /** @var array */
    public $executablePaths = [
        '@backend/yii',
        '@frontend/yii',
        '@console/yii',
        '@api/yii',
    ];

    /** @var array */
    public $generateKeysPaths = [
        '@base/.env'
    ];

    /**
     * Sets given keys to .env file
     */
    public function actionSetKeys()
    {
        $this->setKeys($this->generateKeysPaths);
    }

    /**
     * @throws \yii\base\InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function actionSetup()
    {
        $this->runAction('set-writable', ['interactive' => $this->interactive]);
        $this->runAction('set-executable', ['interactive' => $this->interactive]);
        $this->runAction('set-keys', ['interactive' => $this->interactive]);
        \Yii::$app->runAction('migrate/up', ['interactive' => $this->interactive]);
        \Yii::$app->runAction('rbac-migrate/up', ['interactive' => $this->interactive]);
    }

    /**
     * Truncates all tables in the database.
     * @throws \yii\db\Exception
     */
    public function actionTruncate()
    {
        $dbName = Yii::$app->db->createCommand('SELECT DATABASE()')->queryScalar();
        if ($this->confirm('This will truncate all tables of current database [' . $dbName . '].')) {
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
            $tables = Yii::$app->db->schema->getTableNames();
            foreach ($tables as $table) {
                $this->stdout('Truncating table ' . $table . PHP_EOL, Console::FG_RED);
                Yii::$app->db->createCommand()->truncateTable($table)->execute();
            }
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
        }
    }

    /**
     * Drops all tables in the database.
     * @throws \yii\db\Exception
     */
    public function actionDrop()
    {
        $dbName = Yii::$app->db->createCommand('SELECT DATABASE()')->queryScalar();
        if ($this->confirm('This will drop all tables of current database [' . $dbName . '].')) {
            Yii::$app->db->createCommand("SET foreign_key_checks = 0")->execute();
            $tables = Yii::$app->db->schema->getTableNames();
            foreach ($tables as $table) {
                $this->stdout('Dropping table ' . $table . PHP_EOL, Console::FG_RED);
                Yii::$app->db->createCommand()->dropTable($table)->execute();
            }
            Yii::$app->db->createCommand("SET foreign_key_checks = 1")->execute();
        }
    }

    /**
     * @param string $charset
     * @param string $collation
     * @throws \yii\base\ExitException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function actionAlterCharset($charset = 'utf8mb4', $collation = 'utf8mb4_unicode_ci')
    {
        if (Yii::$app->db->getDriverName() !== 'mysql') {
            Console::error('Only mysql is supported');
            Yii::$app->end(1);
        }

        if (!$this->confirm("Convert tables to character set {$charset}?")) {
            Yii::$app->end();
        }

        $tables = Yii::$app->db->getSchema()->getTableNames();
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();
        foreach ($tables as $table) {
            $command = Yii::$app->db->createCommand("ALTER TABLE {$table} CONVERT TO CHARACTER SET :charset COLLATE :collation")->bindValues([
                ':charset' => $charset,
                ':collation' => $collation
            ]);
            $command->execute();
        }
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
        Console::output('All ok!');
    }


    /**
     * Adds write permissions
     */
    public function actionSetWritable()
    {
        $this->setWritable($this->writablePaths);
    }

    /**
     * Adds execute permissions
     */
    public function actionSetExecutable()
    {
        $this->setExecutable($this->executablePaths);
    }

    /**
     * @param $paths
     */
    private function setWritable($paths)
    {
        foreach ($paths as $writable) {
            $writable = Yii::getAlias($writable);
            Console::output("Setting writable: {$writable}");
            @chmod($writable, 0770);
        }
    }

    /**
     * @param $paths
     */
    private function setExecutable($paths)
    {
        foreach ($paths as $executable) {
            $executable = Yii::getAlias($executable);
            Console::output("Setting executable: {$executable}");
            @chmod($executable, 0750);
        }
    }

    /**
     * @param $paths
     */
    private function setKeys($paths)
    {
        foreach ($paths as $file) {
            $file = Yii::getAlias($file);
            Console::output("Generating keys in {$file}");
            $content = file_get_contents($file);
            $content = preg_replace_callback('/<generated_key>/', function () {
                $length = 32;
                $bytes = openssl_random_pseudo_bytes(32, $cryptoStrong);
                return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
            }, $content);
            file_put_contents($file, $content);
        }
    }

    public function actionGenerateXml()
    {

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $dataCategories = ProductCategory::find()->all();
        $dataProducts = Product::find()->preparePrice()->published()->with('category')->with('brand')->all();


        $root = $dom->createElement('yml_catalog');
        $root->setAttribute('date', date('Y-m-d H:i'));
        $dom->appendChild($root);

        $shop = $dom->createElement('shop');
        $root->appendChild($shop);

        $shop->appendChild($dom->createElement('name', 'Интернет магазин для мастеров татуировки и татуажа'));
        $shop->appendChild($dom->createElement('company', 'Tattoofeel'));
        $shop->appendChild($dom->createElement('url', env('FRONTEND_HOST_INFO')));
        $shop->appendChild($dom->createElement('platform', 'Yii framework'));
        $shop->appendChild($dom->createElement('version', '2.0'));
        $shop->appendChild($dom->createElement('agency', 'Индивидуальный предприниматель Насибян Гагик Гарникович'));
        $shop->appendChild($dom->createElement('email', 'tattoofeel@inbox.ru'));

        $currencies = $dom->createElement('currencies');
        $currency = $dom->createElement('currency');

        $shop->appendChild($currencies);
        $currencies->appendChild($currency);

        $currency->setAttribute('id', 'RUR');
        $currency->setAttribute('rate', '1');

        $shop->appendChild($dom->createElement('delivery', true));
        $shop->appendChild($dom->createElement('pickup', true));

        $categories = $dom->createElement('categories');
        $shop->appendChild($categories);

        foreach ($dataCategories as $category) {
            $categories->appendChild($dom->createElement('category', $category->title))->setAttribute('id', $category->id);
        }

        $offers = $dom->createElement('offers');
        $shop->appendChild($offers);

        foreach ($dataProducts as $product) {
            $offer = $dom->createElement('offer');
            $offer->setAttribute('id', $product->id);

            $offers->appendChild($offer);
            $offer->appendChild($dom->createElement('name', htmlspecialchars($product->title)));
            $offer->appendChild($dom->createElement('vendor', env('FRONTEND_HOST_INFO') . '/brands/' . $product->brand_id . '/'));
            $offer->appendChild($dom->createElement('vendorCode', $product->article));
            $offer->appendChild($dom->createElement('url', env('FRONTEND_HOST_INFO') . '/catalog/' . $product->brand->slug . '/' . $product->slug . '/'));
            $offer->appendChild($dom->createElement('price', strip_tags(str_replace('i', '', $product->getFrontendMinPrice()))));
            $offer->appendChild($dom->createElement('oldprice', strip_tags(str_replace('i', '', $product->getFrontendOldPrice()))));
            $offer->appendChild($dom->createElement('currencyId', 'RUR'));
            $offer->appendChild($dom->createElement('categoryId', $product->category->id));
            $offer->appendChild($dom->createElement('picture', env('FRONTEND_HOST_INFO') . '/storage/source/' . $product->thumbnail_path));
            $offer->appendChild($dom->createElement('delivery', true));
            $offer->appendChild($dom->createElement('pickup', true));

            $delivery_options = $offer->appendChild($dom->createElement('delivery-options'));

            $option = $delivery_options->appendChild($dom->createElement('option'));
            $option->setAttribute('cost', '220');
            $option->setAttribute('days', '1-2');

            $pickup_options = $offer->appendChild($dom->createElement('pickup-options'));
            $option_p = $pickup_options->appendChild($dom->createElement('option'));
            $option_p->setAttribute('cost', '220');
            $option_p->setAttribute('days', '1-2');
            $offer->appendChild($dom->createElement('store', true));
            $offer->appendChild($dom->createElement('description', "<![CDATA[ " . htmlspecialchars(Html::decode($product->body)) . " ]]>"));
            $offer->appendChild($dom->createElement('weight', $product->weight));
            $dimensions = $product->length . '/' . $product->width . '/' . $product->height;
            $offer->appendChild($dom->createElement('dimensions', $dimensions));
            $offer->appendChild($dom->createElement('count', $product->amount));
        }


        if ($dom->saveXML()) {
            echo "SUCCESS";
        } else {
            echo 'ERROR';
        }
        $dom->save('../frontend/web/y3Ysb1dsj92Sg44gxLf8b5Mbarf9cp.xml') or die('XML Create Error');

    }
}
