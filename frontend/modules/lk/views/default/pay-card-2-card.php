<?php

use common\models\UserClientOrder;
use common\models\BankCards;
use yii\helpers\Url;

/**
 * @var UserClientOrder $order
 * @var BankCards $card
 */

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Оплата с карты на карту';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Оплата с карты на карту'];

?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="lk__box">
            <h1 class="h3 card2card-title">Номер заказа <?=$order->order_ms_number?></h1>
            <div class="card2card-pay-sum"><?=$order->totalSumFormat?></div>

            <div class="card2card-card-rect">
                <div class="card2card-card-text-group">
                    <input id="card2card-copy-to-clipboard" type="text" class="card2card-card-number" value="<?=implode(' ', str_split($card->number, 4))?>" disabled />
                    <a href="javascript:void(0);" class="card2card-card-copy-icon" onclick="copyToClipboard();"></a>
                    <div class="card2card-card-owner"><?=strtoupper($card->owner)?></div>
                </div>

                <div class="card2card-card-logo-group">
                    <div class="card2card-card-logo-1"></div>
                    <div class="card2card-card-logo-2"></div>
                </div>
            </div>

            <p class="card2card-info-text">Пожалуйста, оставляйте поле «сообщение получателю» <span class="red">пустым</span>.<br>После завершения перевода нажмите на кнопку «Оплачено, проверяйте».</p>
            <a href="/lk/payed-card-2-card/?order_id=<?=$order->id?>" class="btn _wide">Оплачено, проверяйте</a>
            <p class="card2card-info-small-text">Если в течении 10 минут после совершения перевода с Вами не свяжется наш оператор, пожалуйста, позвоните нам.</p>
            <a href="/" class="card2card-go-back-link">Вернуться на сайт</a>
        </div>
    </div>
</section>

<script>
    function copyToClipboard() {
        const el = document.createElement('textarea');
        let str = document.getElementById('card2card-copy-to-clipboard').value;
        el.value = str.replace(/\s+/g, '');
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);

        $('body').find('.pop-mes').trigger('pop-mes.add', ['Номер банковской карты скопирован в буфер обмена.']);
    }
</script>