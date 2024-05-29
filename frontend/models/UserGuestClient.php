<?php

namespace frontend\models;

use common\models\Currency;
use common\models\ProductPrice;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\Cookie;

class UserGuestClient extends Model
{
    //todo interface -
    public $id = null;
    public $_cart = [];

    public function __construct($config = [])
    {
        $this->_cart = $this->getCookie('cart');
        parent::__construct($config);
    }

    protected function getCookie($key) {
        $value = '[]';
        $resultValue = [];

        $cookies = Yii::$app->request->cookies;

        if (($cookie = $cookies->get($key)) !== null) {
            $value = $cookie->value;
        }

        try {
            $_resultValue = (array) json_decode($value);

            foreach ($_resultValue as $k => $v)
                $resultValue[(int) $k] = $v;

        } catch(\Exception $e) {
            \Yii::error('Cart guest $cookies:json', $e);
        }

        return $resultValue;
    }

    protected function setCookie($key, $value) {
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new Cookie([
            'name' => $key,
            'value' => json_encode($value)
        ]));
    }

    protected function removeArrayCookie($key, $idx) {
        $cookie = $this->getCookie($key);

        if (is_array($cookie)) {
            unset($cookie[$idx]);
            $this->setCookie($key, $cookie);
        }
    }

    protected function setArrayCookie($key, $value, $idx) {
        $cookie = $this->getCookie($key);

        if (is_array($cookie)) {
            $cookie[$idx] = $value;
            $this->setCookie($key, $cookie);
        }
    }

    protected function addArrayCookie($key, $value, $idx) {
        $cookie = $this->getCookie($key);

        if (is_array($cookie)) {
            if (in_array($idx, array_keys($cookie))) {
                $cookie[$idx] = $cookie[$idx] + $value;
            } else $cookie[$idx] = $value;

            $this->setCookie($key, $cookie);
        }
    }

    protected function removeCookie($key) {
        $cookies = Yii::$app->request->cookies;
        $cookies->remove($key);
    }

    public function getDeferred()
    {
        $ids_deferred = $this->getCookie('deferred');
        return Product::find()->where(['in', 'id', $ids_deferred])->all();
    }

    public function isDeferred($pid) {
        return in_array($pid, $this->getCookie('deferred'));
    }

    public function changeDeferred($type, $pid) {
        if (!empty($type))
            $this->setArrayCookie('deferred', $pid, $pid);
        else
            $this->removeArrayCookie('deferred', $pid);
    }

    protected function _addCart($count, $pid) {
        $this->addArrayCookie('cart', $count, $pid);

        //todo -
        if (isset($this->_cart[$pid])) {
            $this->_cart[$pid] = $this->_cart[$pid] + $count;
        } else {
            $this->_cart[$pid] = $count;
        }
    }

    protected function _changeCart($count, $pid) {
        $this->setArrayCookie('cart', $count, $pid);

        //todo -
        $this->_cart[$pid] = $count;
    }

    protected function _removeCart($pid) {
        $this->removeArrayCookie('cart', $pid);

        //todo -
        if (isset($this->_cart[$pid]))
            unset($this->_cart[$pid]);
    }

    public function addCart($count, $pid) {
        $this->_addCart($count, $pid);
        return $this->getCart();
    }

    public function removeCart($pid) {
        $this->_removeCart($pid);
        return $this->getCart();
    }

    public function changeCart($count, $pid) {
        $this->_changeCart($count, $pid);

        $clientPriceValue = 0;
        $product = Product::find()->preparePrice()->andWhere([Product::tableName().'.id'=>$pid])->one();

        if ($product)
            $clientPriceValue = $product->clientPriceValue;

        $price = ProductPrice::getParsePrice(
            $this->_cart[$pid] * $clientPriceValue,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );
        $priceFormat = implode('', [$price->ceil_fr, ' ', $price->cur]);

        return [
            'cart' => $this->getCart(),
            'price' => $priceFormat
        ];
    }

    public function addCartConfigs($configs) {

        foreach((array) $configs as $c )
            if (isset($c['id']) && isset($c['count']) && !empty($c['count']))
                $this->_addCart($c['count'], $c['id']);

        return $this->getCart();
    }

    /**
     * @return object
     */
    public function getCart()
    {
        $cCart = $this->_cart;
        $allCount = $sum = $sumFormat = $sumTotal = 0;
        $linkProducts = [];
        $sum_delivery = (int) $this->getCookie('cart_delivery');

        foreach($cCart as $id => $count) {
            if ($p = Product::find()->preparePrice()->andWhere([Product::tableName().'.id'=>$id])->one()) {
                $sum += $p->clientPriceValue * $count;
                $allCount += $count;
                $linkProducts[$id] = (object) [
                  'id' => $id,
                  'product' => $p,
                  'count' => $count
                ];
            }
        }

        if ($sum) {
            $price = ProductPrice::getParsePrice(
                $sum + $sum_delivery,
                Currency::DEFAULT_CART_PRICE_CUR_ISO
            );
            $sumTotal = $sum + $sum_delivery;
            $sumFormat = implode('', [$price->ceil_fr, ' ', $price->cur]);
        }

        $cart = [
            'count' => $allCount,
            'sumTotal' => $sumTotal,
            'sumFormat' => $sumFormat,
            'linkProducts' => $linkProducts
        ];

        return (object) $cart;
    }

    public function addSumDelivery($sum) {
        $this->setCookie('cart_delivery', $sum*100);
    }

    public function getCartProducts() {
        return $this->getCookie('cart');
    }

    public function clearCart() {
        $this->setCookie('cart', []);
    }

    public function clearDeferred() {
        $this->setCookie('deferred', []);
    }

    public function getDeferredIds() {
        return $this->getCookie('deferred');
    }
}
