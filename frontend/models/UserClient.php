<?php

namespace frontend\models;

use common\models\Currency;
use common\models\ProductPrice;
use common\models\UserClientDeferred;
use common\models\UserClientOrder;
use yii\web\NotFoundHttpException;

class UserClient extends \common\models\UserClient
{
    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function afterLogin()
    {
        $this->processCartAfterLogin();
        parent::afterLogin();
    }

    public function processCartAfterLogin(): void
    {
        $order = $this->getCart();
        $guest = new UserGuestClient();

        // Move orders from guest cart to client cart.
        if (!empty($guest->cartProducts)) {
            $arProducts = array();
            foreach ($guest->cartProducts as $id => $count) {
                $arProducts[$id] = array(
                    'id' => $id,
                    'count' => $count,
                );
            }
            //$order->clearCart(); need to keep previous prods
            $order->setProducts($arProducts);
            $guest->clearCart();
        }

        // Move favorite products from guest to client.
        if (!empty($guest->deferredIds)) {
            foreach ($guest->deferredIds as $pid) {
                $model = Product::findOne($pid);
                if ($model && !$this->isDeferred($pid))
                    $this->link('deferred', $model, ['client_created_at' => time(), 'client_created_by' => $this->id]);
            }

            $guest->clearDeferred();
        }
    }

    public function getCart()
    {
        $order = $this->hasOne(UserClientOrder::class, ['user_id' => 'id'])
            ->where(['isCart' => 1])->one();

        if (!$order) {
            $order = new UserClientOrder();
            $order->user_id = $this->id;
            $order->isCart = 1;
            $order->date = 'now'; // omg it actually has filter applying strtotime to this sh*t
            $order->save();
        }
        return $order;
    }

    public function getDeferred()
    {
        return $this->hasMany(Product::class, ['id' => 'product_id'])
            ->viaTable('{{%user_client_product_deferred}}',
                ['user_id' => 'id']);
    }

    public function isDeferred($pid)
    {
        return (bool)UserClientDeferred::find()
            ->where(['product_id' => $pid, 'user_id' => $this->id])->count();
    }

    public function changeDeferred($type, $pid)
    {
        $model = Product::findOne($pid);

        if (!$model)
            throw new NotFoundHttpException();

        if (!empty($type))
            $this->link('deferred', $model, ['client_created_at' => time(), 'client_created_by' => $this->id]);
        else
            $this->unlink('deferred', $model, true);

    }

    public function removeCart($pid, $coupon_code = '')
    {
        $model = Product::findOne($pid);

        if (!$model)
            throw new NotFoundHttpException();

        return $this->cart->removeProduct($model->id, $coupon_code);

    }

    public function addCart($count, $pid, $sumCount = true, $coupon_code = '', $isGift = false)
    {
        $model = Product::findOne($pid);

        if (!$model)
            throw new NotFoundHttpException();

        return $this->cart->updateProduct($model->id, ['id' => $model->id, 'count' => $count], $sumCount, $coupon_code, $isGift);
    }

    public function changeCart($count, $pid, $coupon_code = '')
    {
        $model = Product::find()->preparePrice()->andWhere([Product::tableName() . '.id' => $pid])->one();

        if (!$model)
            throw new NotFoundHttpException();

        $price = ProductPrice::getParsePrice(
            $count * $model->clientPriceValue,
            Currency::DEFAULT_CART_PRICE_CUR_ISO
        );
        $priceFormat = implode('', [$price->ceil_fr, ' ', $price->cur]);

        return [
            'cart' => $this->addCart($count, $pid, false, $coupon_code),
            'price' => $priceFormat
        ];

    }

    public function addCartConfigs($configs)
    {
        $cart = $this->getCart();

        // при добавлениие товара в корзину сбрасывать купон
        $cart->sum_discount = null;
        $cart->coupon_id = null;
        $cart->save(false);

        return $cart->addProducts($configs, true);
    }

}
