<?php

namespace console\controllers;

use common\models\Currency;
use common\models\Product;
use common\models\ProductCategory;
use common\models\ProductCategoryConfig;
use common\models\UserClientProfile;
use yii\console\Controller;
use yii\helpers\Console;


class SyncController extends Controller
{

    public $keySyncIsStartCurrency;
    public $keySyncIsStartProduct;
    public $keySyncIsStartProductConfig;
    public $keySyncIsStartProductCat;

    public function init() {
        $this->keySyncIsStartCurrency = Currency::syncProvider()->getStartKeyValue();
        $this->keySyncIsStartProduct = Product::syncProvider()->getStartKeyValue();
        $this->keySyncIsStartProductConfig = ProductCategoryConfig::syncProvider()->getStartKeyValue();
        $this->keySyncIsStartProductCat = ProductCategory::syncProvider()->getStartKeyValue();
    }

    public function actionSyncCurrency($uid = -1) {
        if ($this->keySyncIsStartCurrency) {
            Console::output("Already currency sync");
        } else {
            Currency::sync($uid);
            Console::output("Currency sync success");
        }
    }

    public function actionSyncProducts($uid = -1) {
        if ($this->keySyncIsStartProduct) {
            Console::output("Already product sync");
        } else {
            Product::sync($uid);
            Console::output("Product sync success");
        }
    }

    public function actionSyncProductsOne($uid = -1, $ms_id = null) {

        if ($this->keySyncIsStartProduct) {
            Console::output("Already product sync");
        } else {
            Product::sync($uid, $ms_id);
            Console::output("Product sync success");
        }
    }

    public function actionSyncProductConfig($uid = -1) {
        if ($this->keySyncIsStartProductConfig) {
            Console::output("Already product config sync");
        } else {
            ProductCategoryConfig::sync($uid);
            Console::output("Product config sync success");
        }
    }

    public function actionSyncProductCategory($uid = -1) {
        if ($this->keySyncIsStartProductCat) {
            Console::output("Already product category sync");
        } else {
            ProductCategory::sync($uid);
            Console::output("Product category sync success");
        }
    }

    public function actionSyncClient($profileId) {
        UserClientProfile::findOne($profileId)->sync();
        Console::output("Client sync success");
    }

}
