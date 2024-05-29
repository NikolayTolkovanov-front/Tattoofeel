<?php
/**
 * @var $productsRecently
 */

use common\models\Coupons;
use common\models\UserClientOrder;
use frontend\widgets\products\row\ProductsRow;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/** @var Coupons $coupon */
/** @var UserClientOrder $cart */
/** @var UserClientOrder $model */
/** @var $productsRecently ActiveDataProvider*/
/** @var $cartInfoMessage string */

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Корзина';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Корзина'];

if (isset($_GET['order_error']) && $_GET['order_error']) {
    $ms_error = base64_decode(trim($_GET['order_error'], "/"));
    $ms_error = json_decode($ms_error, true);
    if (isset($ms_error['msg'])) {
        Yii::$app->getSession()->setFlash('alert', [
            'body' => $ms_error['msg'],
            'options' => ['class' => 'alert-success']
        ]);
    }
}

?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Корзина</h1>
                    <?php if ($cartInfoMessage) { ?>
                        <div class="lk-cart-info-message">
                            <div class="lk-cart-info-message-title">
                                К сожалению, часть товаров закончилась, мы убрали их из корзины:
                            </div>
                            <br/>
                            <?=$cartInfoMessage?>
                        </div>
                    <?php } ?>
                    <div class="lk-table-wrap">
                        <table class="lk-table-cart" id="lk-cart-list">
                            <?= $this->render('_cart_list', ['cart' => $cart]) ?>
                        </table>
                    </div>
                    <?php if (!empty($cart->linkProducts)) { ?>
                        <?php if ($model) { ?>
                            <div class="lk-cart-coupon">
                                <input id="lk-cart-coupon-input" type="text" name="coupon" value="<?=$coupon ? $coupon->coupon_code : ''?>" placeholder="Введите промокод" />
                                <div class="lk-cart-del-coupon">&times;</div>
                                <button id="lk-cart-coupon-btn" type="button" class="btn _big">Ок</button>
                                <div class="lk-cart-coupon-msg"><?=$coupon ? (int)$cart->sumDiscountFormat ? ('Скидка составит ' . $cart->sumDiscountFormat) : 'Промокод применен' : ''?></div>
                            </div>
                            <div class="lk-cart-total">Товаров на сумму: <span id="lk-cart-sum"><?= $cart->sumFormat ?></span></div>
                            <div class="lk-cart-total">Итого: <span id="lk-cart-sum-without-discount"><?= $cart->sumWithoutDiscountFormat ?></span></div>
                            <div class="lk-profile-btn">
                                <a href="/lk/order-checkout/" class="btn _big" data-pjax="0">Оформить заказ</a>
                            </div>
                        <?php } else { ?>
                            <div class="lk-cart-total">Товаров на сумму: <span id="lk-cart-sum"><?= $cart->sumFormat ?></span></div>
                            <div class="lk-profile-btn">
                                <a href="<?= Url::to(['/lk/login']) ?>" class="btn _big _black" style="margin-bottom:10px;">Авторизация</a>
                                <a href="#" class="btn _big _black js-show-buy-one-click-cart-form">Купить в один клик</a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
        </div>
    </div>
</section>

<?php if ($productsRecently->getTotalCount()) { ?>
    <section id="lk-recently-row">
        <div class="box-white">
            <div class="container container-slider-row">
                <?php try {
                   echo ProductsRow::widget([
                        'title' => 'Популярные товары',
                        'dataProvider' => $productsRecently
                    ]);
                } catch (Exception $e) {
                    Yii::error($e->getMessage(), 'error');
                } ?>
            </div>
        </div>
    </section>
<?php } ?>

<div class="my-modal">
    <div id="buy-one-click-cart-form" class="modal-form"></div>
</div>
