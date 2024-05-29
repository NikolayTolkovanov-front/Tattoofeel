<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var \common\models\UserClientOrder $order
 * @var \common\models\Commission $commission
 * @var \common\models\PaymentTypes $paymentTypes
 */
?>


<div class="modal-close"></div>
<h2 class="h2 center">Оплата заказа</h2>

<?php $form = ActiveForm::begin(['action' => '/change-pay/', 'options' => ['class' => 'pay-form']]) ?>
<p class='help-block-error'></p>

<div class="final-block">
    <!--Варианты оплаты-->
    <div class="left-side-final-block">
        <div class="pay__variant">
            <input name="order_id" type="hidden" value="<?=$order->id?>" />
            <?php foreach ($paymentTypes as $key => $item):?>
                <?php
                if (4 == $item->id && (!Yii::$app->client->identity->profile->sale_ms_id || Yii::$app->client->identity->profile->sale_ms_id == 'Скидка 1')) {
                    continue;
                }

                if ((1 == $item->id && Yii::$app->client->identity->profile->hide_cash) || (2 == $item->id && Yii::$app->client->identity->profile->hide_card)) {
                    continue;
                }
                ?>
                <label style="display: block;" class="pay__variant-item  <?php if (!$key) echo 'pay__check';?>">
                    <?php
                    if (1 == $item->id) {
                        $img = '/img/nalik.svg';
                        $title = "Наличными";
                        $subtitle = "При получении товара";
                    } elseif (2 == $item->id) {
                        $img = '/img/visa_card.svg';
                        $title = "VISA, MasterCard, МИР";
                        $subtitle = "Предоплата на сайте";
                    } elseif (3 == $item->id) {
                        $img = '/img/checking.svg';
                        $title = "Расчётный счёт";
                        $subtitle = "Отсрочка или предоплата";
                    } elseif (4 == $item->id) {
                        $img = '/img/card2card.svg';
                        $title = "Перевод";
                        $subtitle = "С карты на карту";
                    }
                    ?>
                    <div class="pay__variant-img" style="background-image: url(<?=$img?>)"></div>
                    <div class="pay__variant-item-wrap">
                        <div class="pay__variant-item-left">
                            <span class="pay__variant-item-left-title"><?=$title?></span>
                            <span class="pay__variant-item-left-desk"><?=$subtitle?></span>
                        </div>
                    </div>
                    <div class="pay__variant-item-right">
                        <span></span>
                    </div>
                    <input style="opacity: 0; position: absolute;" name="payment_type" type="radio" value="<?=$item->id?>" <?php if ($order->payment_type == $item['id']) echo 'checked';?> />
                    <span class="balon"></span>
                </label>
            <?php endforeach;?>
<!--            <div class="pay__variant-info-text"><span class="red-text">Комиссия 3%</span> за наложенный платёж. Если хотите оплатить без комиссии, выберите <span class="underline-text">VISA</span>, либо получите <span class="green-text">скидку 2%</span>, оплатив переводом на карту.</div>-->
            <div class="pay__variant-info-text"><?=$commission ? $commission->text : ''?></div>
        </div>
    </div>

    <?php
    if ($commission) {
        $commission_sum = ceil((($order->sum_delivery + $order->sum_buy) / 100) / 100 * $commission->percent);
    }
    ?>

    <div class="right-side-final-block">
        <div class="order__result">
            <div class="order__result-item">
                <span class="order__result-item-title">Товары</span>
                <span id="product-sum" data-product-price="<?=$order->sum_buy / 100?>"
                      class="order__result-item-price"><?=$order->sum_buy / 100?> <span class="rub">i</span></span>
            </div>
            <div class="order__result-item">
                <span class="order__result-item-title">Доставка</span>
                <span id="delivery-sum" class="order__result-item-price"><?=$order->sum_delivery / 100?> <span class="rub">i</span></span>
            </div>
            <div class="order__result-item">
                <span class="order__result-item-title">Комиссия</span>
                <span id="commission-sum" class="order__result-item-price"><?=$order->commissionSum / 100?> <span class="rub">i</span></span>
            </div>
            <div class="order__result-item">
                <span class="order__result-item-title">Скидка по промокоду</span>
                <span id="discount-sum" data-discount-sum="<?= floor(((int)$order->sum_discount + (int)$order->sum_delivery_discount) / 100) ?>"
                      class="order__result-item-price"><?=$order->totalSumDiscountFormat?></span>
            </div>
            <div class="order__result-item">
                <span class="order__result-item-title last">Итого к оплате</span>
                <span id="total-sum" class="order__result-item-price"><?=$order->totalSumFormat?></span>
            </div>
        </div>
        <div class="order_load">
            <div class="insurance-info-block">
                <div class="insurance-info-block-title">Страховка за наш счёт</div>
                <div class="insurance-info-block-text">Если транспортная компания потеряет или повредит Ваш заказ, мы оперативно вернём деньги, либо вышлем новый товар.</div>
            </div>
            <button class="btn _wide" type="submit">Отправить</button>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>
