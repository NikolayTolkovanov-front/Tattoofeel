<?php
/**
 * @var $productsRecently
 * @var \common\models\UserClientOrder $order
 */
use frontend\widgets\products\row\ProductsRow;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Корзина | Оплата';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Корзина', 'url' => Url::to(['/lk/cart'])];
$this->params['breadcrumbs'][] = ['label' => 'Оплата'];

//$total_sum = (int)$order->sum_buy + (int)$order->sum_delivery + (int)$order->commissionSum + (int)$order->sum_delivery_discount + (int)$order->sum_delivery_discount;
$total_sum = (int)$order->sum_buy + (int)$order->sum_delivery + (int)$order->commissionSum - (int)$order->sum_discount - (int)$order->sum_delivery_discount;
?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Оплата</h1>

                    <p>Осуществляется переход к оплате...</p>
                    <script src="https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js"></script>
                    <form name="TinkoffPayForm" onsubmit="pay(this); return false;">
                        <input type="hidden" name="terminalkey" value="<?=env("TINKOFF_TERMINAL_KEY")?>">
                        <input type="hidden" name="frame" value="false">
                        <input type="hidden" name="language" value="ru">
                        <input type="hidden" name="amount" value="<?=($total_sum) / 100?>" required>
                        <input type="hidden" name="order" value="<?=$order->id?>">
                        <input type="hidden" name="description" value="Заказ ID: <?=$order->id?>">
                        <input type="hidden" name="name" value="<?=$order->user->username?>">
                        <input type="hidden" name="email" value="<?=$order->user->email?>">
                        <input type="hidden" name="phone" value="">
                        <input id="tinkoff-pay-btn" class="btn _big" type="submit" value="Оплатить" style="display: none;"/>
                    </form>
                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        //console.log('click');
        $('#tinkoff-pay-btn').click();
    });
</script>