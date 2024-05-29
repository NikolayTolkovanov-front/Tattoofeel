<?php
use frontend\helpers\ForURL;
use yii\helpers\Url;
?>

<div class="lk-menu">
    <a <?= ForURL::isActive('default', !Yii::$app->user->isGuest ? 'index' : 'login')?> data-pjax="0" href="<?=Url::to(['/lk'])?>"><?=!Yii::$app->user->isGuest ? 'Профиль' : 'Авторизация'?></a>
    <a <?= ForURL::isActive('default', 'cart')?> data-pjax="0" href="<?= Url::to(['/lk/cart']) ?>">Корзина</a>
    <a <?= ForURL::isActive('default', 'deferred')?> data-pjax="0" href="<?= Url::to(['/lk/deferred']) ?>">Любимые товары</a>
    <?php if (!Yii::$app->user->isGuest):?>
        <a <?= ForURL::isActive('default', 'orders')?> data-pjax="0" href="<?= Url::to(['/lk/orders']) ?>">Мои заказы (<?=count(Yii::$app->getUser()->identity->openOrders)?>)</a>
    <?php endif;?>
    <?php if ((int)Yii::$app->getUser()->identity->is_manager):?>
        <a <?= ForURL::isActive('default', 'login-as')?> data-pjax="0" href="<?= Url::to(['/lk/login-as']) ?>">Войти как...</a>
    <?php endif;?>
    <?php if ((int)Yii::$app->getUser()->identity->id === 23):?>
        <a <?= ForURL::isActive('default', 'test')?> data-pjax="0" href="<?= Url::to(['/lk/test']) ?>">Тестовая страница</a>
    <?php endif;?>
    <?php if (!Yii::$app->user->isGuest):?>
        <a <?= ForURL::isActive('default', 'logout')?> data-pjax="0" href="<?= Url::to(['/lk/logout']) ?>">Выйти из профиля</a>
    <?php endif;?>
</div>
