<?php
/**
 * @var $productsRecently
 *
 */
use yii\helpers\Url;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Безналичная оплата';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Безналичная оплата'];

?>

<section style="padding:20px 0 40px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Безналичная оплата</h1>

                    <script src="https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js"></script>
                    <form class="TinkoffPayForm" name="TinkoffPayForm" onsubmit="pay(this); return false;">
                        <input type="hidden" name="terminalkey" value="<?=env("TINKOFF_TERMINAL_KEY")?>">
                        <input type="hidden" name="frame" value="true">
                        <input type="hidden" name="language" value="ru">
                        <div>
                            <input class="tinkoffPayRow" type="text" name="amount" value="" placeholder="Сумма заказа *" required>
                        </div>
                        <div>
                            <input class="tinkoffPayRow" type="text" name="order" value="" placeholder="Номер заказа *" required>
                        </div>
                        <input type="hidden" name="description" value="Оплата заказа на сайте tattoofeel" placeholder="Описание заказа">
                        <input type="hidden" name="name" placeholder="ФИО плательщика">
                        <input type="hidden" name="email" placeholder="E-mail">
                        <input type="hidden" name="phone" placeholder="Контактный телефон">
                        <input class="btn _big" type="submit" value="Отправить" />
                    </form>

                    <div class="pay__tinkoff-text">
                        <p>Завершая оформление заказа, я даю своё согласие на обработку персональных данных и подтверждаю ознакомление со сроками хранения товара в соответствии с указанными здесь условиями.</p>
                        <p>В соответствии с ФЗ №54-ФЗ кассовый чек при онлайн-оплате на сайте будет предоставлен в электронном виде на указанный при оформлении заказа номер телефона.</p>
                    </div>
                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
        </div>
    </div>
</section>
