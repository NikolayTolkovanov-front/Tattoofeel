<?php

namespace console\controllers;

use common\models\Brand;
use common\models\ProductCategory;
use frontend\models\Product;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Html;

/**
 * Class ExportYmlController
 * @package console\controllers
 */

// command line: php yii export-yml

class ExportYmlController extends Controller
{
    const HEADER = '<?xml version="1.0" encoding="UTF-8"?>';
    const SITE_URL = 'https://tattoofeel.ru';

    private $filePath;

    public $name = 'Tattoofeel';
    public $company = 'Tattoofeel';
    //public $content;

    private function cleanStr($str) {
        $replace = [
            '™' => '',
            'ü' => 'u',
            'é' => 'e',
            '–' => '-',
            '“' => '"',
            '”' => '"',
        ];
        foreach ($replace as $k => $v) {
            $str = str_replace($k, $v, $str);
        }
        return htmlentities($str);
    }

    public function init() {
        $this->filePath = __DIR__.'/../../frontend/web/tattoofeel.yml';
    }

    public function actionIndex()
    {
        //print_r($this->getCategories());
        $content = self::HEADER;
        $content .= '<yml_catalog date="'.date("Y-m-d\\TH:i:sP").'">';
        $content .= '<shop>';
        $content .= '<name>'.$this->name.'</name>';
        $content .= '<company>'.$this->company.'</company>';
        $content .= '<url>'.self::SITE_URL.'</url>';
        $content .= '<currencies><currency id="RUR" rate="1"/></currencies>';

        $content .= '<categories>';
        $categories = $this->getCategories();
        foreach ($categories as $id => $cat) {
            $content .= '<category id="'.$cat['id'].'">'.$cat['title'].'</category>';
        }
        $content .= '</categories>';

        $content .= '<offers>';
        $products = $this->getProducts();
        foreach ($products as $id => $product) {
            $content .= '<offer id="'.$product['id'].'">';

            if ($product['name']) {
                $content .= '<name>'.$this->cleanStr($product['name']).'</name>';
            }

            if ($product['vendor']) {
                $content .= '<vendor>'.$this->cleanStr(str_replace('™', '', $product['vendor'])).'</vendor>';
            }

            if ($product['vendorCode']) {
                $content .= '<vendorCode>'.$product['vendorCode'].'</vendorCode>';
            }

            if ($product['slug']) {
                $content .= '<url>'.self::SITE_URL.'/catalog/'.$categories[$product['categoryId']]['slug'].'/'.$product['slug'].'/</url>';
            }

            if ($product['price']) {
                $content .= '<price>'.$product['price'].'</price>';
                $content .= '<currencyId>RUR</currencyId>';
            }

            if ($product['categoryId']) {
                $content .= '<categoryId>'.$categories[$product['categoryId']]['id'].'</categoryId>';
            }

            if ($product['picture']) {
                $content .= '<picture>'.$product['picture'].'</picture>';
            }

            if ($product['description']) {
                $content .= '<description><![CDATA['.$product['description'].']]></description>';
            }

            if ($product['country']) {
                $content .= '<country_of_origin>'.$this->cleanStr($product['country']).'</country_of_origin>';
            }

            if ($product['weight']) {
                $content .= '<weight>'.$product['weight'].'</weight>';
            }

            if ($product['dimensions']) {
                $content .= '<dimensions>'.$product['dimensions'].'</dimensions>';
            }

            $content .= '</offer>';
        }
        $content .= '</offers>';

        $content .= '</shop>';
        $content .= '</yml_catalog>';

        $this->writeFile($content);

        echo "Command is completed.\n";
    }

    private function getProducts()
    {
        $products = Product::find()
            ->preparePrice()
            //->prepareConfig(!empty($filters) && is_array($filters))
            //->andWhere([Product::tableName().'.is_discount' => 1])
            ->andWhere([Product::tableName().'.status' => 1])
            ->andWhere([Product::tableName().'.is_ms_deleted' => 0])
            ->asArray()
            ->all();

        //print_r($products);
        $arProds = array();

        $brands = $this->getBrands();

        foreach ($products as $product) {
            if ($product['clientPriceValue']) {
                $arProds[$product['id']] = array(
                    'id' => $product['id'],
                    'name' => $product['title'],
                    'vendor' => $product['brand_id'] ? $brands[$product['brand_id']]['title'] : '',
                    'vendorCode' => $product['article'],
                    'slug' => $product['slug'],
                    'price' => $product['clientPriceValue'] / 100,
                    'categoryId' => $product['category_ms_id'] ? $product['category_ms_id'] : '',
                    'picture' => $product['thumbnail_path'] ? self::SITE_URL.'/storage/source/'.$product['thumbnail_path'] : '',
                    'description' => $product['body'],
                    'country' => $product['manufacturer'],
                    'weight' => $product['weight'],
                    'dimensions' => $product['length'] && $product['width'] && $product['height'] ? $product['length'].'/'.$product['width'].'/'.$product['height'] : '',
                );
            }
        }

        return $arProds;
    }

    private function getCategories()
    {
        $categories = ProductCategory::find()
            ->andWhere([ProductCategory::tableName().'.status' => 1])
            ->asArray()
            ->all();

        $arCats = array();

        foreach ($categories as $category) {
            $arCats[$category['ms_id']] = array(
                'id' => $category['id'],
                'title' => $category['title'],
                'slug' => $category['slug'],
            );
        }

        return $arCats;
    }

    private function getBrands()
    {
        $brands = Brand::find()
            ->andWhere([Brand::tableName().'.status' => 1])
            ->asArray()
            ->all();

        $arBrands = array();

        foreach ($brands as $brand) {
            $arBrands[$brand['slug']] = array(
                'id' => $brand['id'],
                'title' => $brand['title'],
                'slug' => $brand['slug'],
            );
        }

        return $arBrands;
    }

    private function writeFile($content)
    {
        $yml = fopen($this->filePath, 'w');

        if (fwrite($yml, $content) === FALSE) {
            echo "Failed to write to file.\n";
        }

        fclose($yml);
    }
}
