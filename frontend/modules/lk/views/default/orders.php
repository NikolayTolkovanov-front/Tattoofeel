<?php
/**
 * @var $productsRecently
 */

use common\models\Currency;
use common\models\ProductPrice;
use common\models\UserClientOrder;
use frontend\widgets\common\Icon;
use frontend\widgets\products\row\ProductsRow;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Мои заказы';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Мои заказы'];
?>

<section class="orders-list" style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Мои заказы</h1>
                    <div class="lk-table-wrap">
                        <?php if (empty($orders)) { ?>
                            <div class="lk-table">
                                <div class="lk-table__row">
                                    <div class="lk-table-fd-number">Заказов нет</div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <table class="tabl-ordrs-new">
                                <tr class="tr-ordrs-header">
                                    <th class="thd-ordrs-new"></th>
                                    <th class="thd-ordrs-new">№ заказа</th>
                                    <th class="thd-ordrs-new">Дата</th>
                                    <th class="thd-ordrs-new">Сумма</th>
                                    <th class="thd-ordrs-new">Статус</th>
                                    <!--                                    <th class="thd-ordrs-new">Оплата</th>-->
                                    <th class="thd-ordrs-new">Способ оплаты</th>
                                </tr>

                                <?php foreach ($orders as $key => $order) { ?>
                                    <tr class="tr-ordrs is-collapsed <?= $key ? '' : 'first-row' ?>">
                                        <td class="tcell-ordrs-new">
                                            <div class="orders-head-arrow"></div>
                                        </td>
                                        <td class="tcell-ordrs-new"><strong class="only-mbl">Номер заказа:</strong><a
                                                    href="#"
                                                    class="orders-head-number"><?= $order->order_ms_number ?></a></td>
                                        <td class="tcell-ordrs-new orders-date"><strong class="only-mbl">Дата:</strong>
                                            <?= Yii::$app->formatter->asDatetime(
                                                $order->date ? $order->date :
                                                    ($order->created_at ? $order->created_at : $order->client_created_at),
                                                'php:d.m.Y<\B\r>'
                                            ) ?>
                                            <span>
                                                <?= Yii::$app->formatter->asTime(
                                                    $order->date ? $order->date :
                                                        ($order->created_at ? $order->created_at : $order->client_created_at),
                                                    'php:H:i'
                                                ) ?>
                                            </span>
                                        </td>
                                        <td class="tcell-ordrs-new order__sum"><strong
                                                    class="only-mbl">Сумма:</strong><?= $order->totalSumFormat ?></td>
                                        <td class="tcell-ordrs-new"><strong
                                                    class="only-mbl">Статус:</strong><?= !is_null($order->status) ? $order->state->title : 'Не известен' ?>
                                            <?php if ($order->status == UserClientOrder::ORDER_STATUS_WAITING_FOR_PAYMENT): ?>
                                                <?php if ($order->payment_type == UserClientOrder::PAYMENT_TYPE_TINKOFF): ?>
                                                    <br/> <a class="orders-link action-pay"
                                                             href="/lk/pay/?order_id=<?= $order->id ?>">Оплатить</a>
                                                <?php endif; ?>
                                                <?php if ($order->payment_type == UserClientOrder::PAYMENT_TYPE_CARD_2_CARD): ?>
                                                    <br/> <a class="orders-link action-pay"
                                                             href="/lk/pay-card-2-card/?order_id=<?= $order->id ?>">Оплатить</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="tcell-ordrs-new"><strong class="only-mbl">Способ оплаты:</strong>
                                            <?= $order->payment_type ? $order->payment->title : '' ?><br>
                                            <?php if (!$order->status_pay): ?>
                                                <a class="orders-link action-chang js-show-pay-form" href="#"
                                                   data-order_id="<?= $order->id ?>">Изменить</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <tr id="order-product-list-<?= $order->id ?>" class="tr-ordrs-content">
                                        <td colspan="7">
                                            <div class="tr-ordrs-content-wrap">
                                                <div class="flex-row row__ord-meta">
                                                    <div class="odr-meta__col-l">
                                                        <strong>Служба
                                                            доставки:</strong> <?= $order->deliveryService->title ?: 'Не известно' ?>
                                                        <br>
                                                        <!--                                                    <strong>Дата доставки:</strong> 15-17 сентября <br>-->
                                                        <?php if ($order->delivery_type == UserClientOrder::DELIVERY_TYPE_PICKUP && $order->delivery_service_id == UserClientOrder::DELIVERY_SERVICE_SDEK): ?>
                                                            <strong>Трек
                                                                номер:</strong> <?= $order->track_number ? '<a style="color: #0098e9; text-decoration: underline;" href="https://www.cdek.ru/ru/tracking?order_id=' . $order->track_number . '" target="_blank">' . $order->track_number . '</a>' : 'Не известно' ?>
                                                            <br>
                                                        <?php endif; ?>
                                                        <!--                                                    <strong>Подробнее:</strong> в городе отправителя г Санкт-Петербург 24 транзитный пункт<br>-->
                                                    </div>
                                                    <div class="odr-meta__col-r">
                                                        <?php if ($order->delivery_type == UserClientOrder::DELIVERY_TYPE_PICKUP): ?>
                                                            <?php
                                                            $work_hours = $phones = 'Не известно';
                                                            if ($order->pvz_info) {
                                                                $arr = explode(', Режим работы: ', $order->pvz_info);
                                                                if (isset($arr[1]) && !empty($arr[1])) {
                                                                    $phones = str_replace('Тел: ', '', $arr[0]);
                                                                    $work_hours = $arr[1];
                                                                }
                                                            }
                                                            ?>
                                                            <strong>Пункт
                                                                выдачи:</strong> <?= $order->address_delivery_pvz ?: 'Не известно' ?>
                                                            <br>
                                                            <strong>Режим работы:</strong> <?= $work_hours ?><br>
                                                            <strong>Телефон:</strong> <?= $phones ?><br>
                                                        <?php endif; ?>

                                                        <?php if ($order->delivery_type == UserClientOrder::DELIVERY_TYPE_PICKUP_FROM_WAREHOUSE): ?>
                                                            <strong>Пункт
                                                                выдачи:</strong> г. Москва, Братиславская 19к2, оф. 204
                                                            <br>
                                                            <strong>Режим работы:</strong> 10:00 - 20:00<br>
                                                        <?php endif; ?>

                                                        <?php if ($order->delivery_type == UserClientOrder::DELIVERY_TYPE_COURIER ||
                                                            $order->delivery_service_id == UserClientOrder::DELIVERY_SERVICE_OUR_COURIER): ?>
                                                            <strong>Адрес
                                                                доставки:</strong> <?= $order->address_delivery ?: 'Не указан' ?>
                                                            <br>
                                                        <?php endif; ?>

                                                        <?php if ($order->delivery_service_id == UserClientOrder::DELIVERY_SERVICE_OUR_COURIER): ?>
                                                            <strong>Телефон:</strong> +7 965 302-21-55<br>
                                                        <?php endif; ?>

                                                        <strong>Кол-во
                                                            мест:</strong> <?= $order->places_count ?: 'Не известно' ?>
                                                        &nbsp; &nbsp;
                                                        <strong>Вес:</strong> <?= $order->totalWeight ? '~' . $order->totalWeight . ' кг' : 'Не известно' ?>
                                                        <br>
                                                    </div>
                                                </div>

                                                <?php if (!empty($order->linkProducts) && in_array($order->state->ms_title, UserClientOrder::$order_statuses_can_change_products)): ?>
                                                    <?php foreach ($order->linkProducts as $lp): ?>
                                                        <?= $this->render('_order_item', ['lp' => $lp]) ?>
                                                    <?php endforeach; ?>

                                                    <?php if (!$order->coupon_id): ?>
                                                        <?php $form = ActiveForm::begin([
                                                            'enableClientValidation' => false,
                                                            'enableAjaxValidation' => false,
                                                            'action' => Url::to(['/catalog/filter']),
                                                            'options' => [
                                                                'method' => "post",
                                                                'id' => 'header-not-menu-search-' . $order->id,
                                                                'class' => 'menu-search'
                                                            ]
                                                        ]); ?>
                                                        <?= AutoComplete::widget([
                                                            'name' => 'term',
                                                            'value' => Yii::$app->request->post('term'),
                                                            'options' => [
                                                                'placeholder' => "Название или код товара...",
                                                                'id' => 'menu-not-search-' . $order->id,
                                                                'class' => 'placeholder'
                                                            ],
                                                            'clientOptions' => [
                                                                'source' => Url::to(['/search']),
                                                                'minLength' => '2',
                                                                'autoFocus' => true,
                                                                'classes' => [
                                                                    "ui-autocomplete" => "ui-autocomplete-search-menu"
                                                                ],
                                                                'select' => new JsExpression("function(e, ui) {
                                                                    setTimeout(function() {
                                                                        let rows = $('#order-product-list-" . $order->id . "').find('.lk-table__row');
                                                                        let ids = [];
                                                                        
                                                                        rows.each(function (index, value) {
                                                                            ids.push(+$(this).find('.number').attr('data-product-id'));
                                                                        });
                                                                        
                                                                        if (ids.indexOf(ui.item.id) !== -1) {
                                                                            $('body').find('.pop-mes').trigger('pop-mes.add', ['Такой товар уже есть в заказе']);
                                                                        } else {
                                                                            $.get('/lk/get-order-item/?id=' + ui.item.id)
                                                                                .done(function(data) {
                                                                                    if (data === '') {
                                                                                        $('body').find('.pop-mes').trigger('pop-mes.add', ['Данного товара нет в наличии']);
                                                                                    } else {
                                                                                        $(data).insertBefore('#header-not-menu-search-" . $order->id . "');
                                                                                        $('#order-product-list-" . $order->id . "').find('.lk-table-fd-save').show();
                                                                                    }
                                                                                });
                                                                        }
                                                                        
                                                                        $('#menu-not-search-" . $order->id . "').val('');
                                                                    }, 50);
                                                                    //console.log(ui.item.id, ui.item); 
                                                                }"),
                                                                'create' => new JsExpression('function(event, ui) {
                                                                    $(this).closest("tr").find("input.ui-autocomplete-input").autocomplete("instance")._renderItem = function(ul, item) {
                                                                        var label  = item.label;
                                                                        return $("<li></li>").data("item.autocomplete", item)
                                                                            .append(
                                                                                "<a href=\"javascript:void(0);\">" +
                                                                                "<span class=\"ui-sm-it__pict\" style=\"background-image:url(" +
                                                                                item.imgUrl + ")\"></span>" +
                                                                                "<span class=\"ui-sm-it__label\">" + label + "</span>" +
                                                                                "<span class=\"ui-sm-it__price\">" + item.price + "</span>" +
                                                                                "</a>"
                                                                            ).appendTo(ul);
                                                                    };
                                                                }'),
                                                            ],
                                                        ]); ?>
                                                        <?= Icon::widget(['name' => 'loader', 'width' => '18px', 'height' => '18px',
                                                            'options' => ['class' => 'icon icon-loader']]) ?>
                                                        <?php ActiveForm::end() ?>

                                                        <?php if (!$order->status_pay): ?>
                                                            <div class="lk-table-fd-save">
                                                                <a class="btn _wide js-lk-order-save" href="#"
                                                                   data-order-id="<?= $order->id ?>">Сохранить</a>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
        </div>
    </div>
</section>

<?php if ($productsRecently->getTotalCount()) { ?>
    <section>
        <div class="box-white">
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Популярные товары',
                    'dataProvider' => $productsRecently
                ]) ?>
            </div>
        </div>
    </section>
<?php } ?>

<div class="my-modal">
    <div id="pay-form" class="modal-form"></div>
</div>