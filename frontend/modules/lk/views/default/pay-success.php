<?php

use frontend\widgets\products\row\ProductsRow;
use yii\helpers\Url;

/**
 * @var $productsRecently
 * @var $order
 * @var string $delivery_info Инофрмация о доставке
 */

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Корзина | Заказ оформлен';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Корзина', 'url' => Url::to(['/lk/cart'])];
$this->params['breadcrumbs'][] = ['label' => ' Заказ оформлен'];

?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <!--<h1 class="h3">Оплата прошла успешно</h1>-->
                    <h1 class="h3">Заказ успешно оформлен!</h1>
                    <p>С вами свяжется наш менеджер.</p>
                    <p>
                        🎁 Узнавайте первыми о новинках, скидках и спецпредложениях интернет-магазина TATTOOFEEL!
                        <br>
                        Подписывайтесь на удобный мессенджер ниже. 👇 <br>
                        <a href="tg://resolve?domain=tattoofeel_bot&start=c1694509062088-ds" target="_blank" class="telegram-subs">
                            <i class="fa fa-telegram"></i> Telegram
                        </a>
                        <a href="https://vk.com/app6379730_-159189826#l=3" target="_blank" class="vk-subs">
                            <i class="fa fa-vk"></i> Vk.com
                        </a>
                    </p>
                    <?php /* if (!Yii::$app->user->isGuest):?>
                        <a data-pjax="0" href="<?=Url::to(['/lk/orders'])?>" class="btn _big">Мои заказы</a>
                    <?php else:?>
                        <a data-pjax="0" href="<?=Url::home()?>" class="btn _big">На главную</a>
                    <?php endif;*/ ?>
                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
        </div>
    </div>
</section>

<?php if($productsRecently->getTotalCount()) { ?>
    <section id="lk-recently-row">
        <div class="box-white">
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Популярные товары',
                    'dataProvider' => $productsRecently
                ])?>
            </div>
        </div>
    </section>
<?php } ?>

<?php
$response = array(
    'ecommerce' => array(
        'currencyCode' => 'RUB',
        'purchase' => array(
            'actionField' => array(
                'id' => $order->id,
            ),
        ),
    )
);

foreach ($order->linkProducts as $pos) {
    $response['ecommerce']['purchase']['products'][] = array(
        'id' => $pos->product_id,
        'name' => $pos->product->title,
        'price' => floatval($pos->price / 100),
        'brand' => $pos->product->brand_->title ?: '',
        'category' => $pos->product->category->title,
        'quantity' => $pos->count,
    );
}
?>

<script>
    window.dataLayer.push(<?=json_encode($response, JSON_UNESCAPED_UNICODE)?>);
    VK.Goal('purchase', {value: <?=floatval($order->sum_buy / 100)?>});
</script>
