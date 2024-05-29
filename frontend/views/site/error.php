<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>

<?php if ($exception->statusCode === 404):?>
    <div class="box" style="padding-top:1px">
        <div class="container">
            <div class="page__404__content">
                <div class="page__404__text-block">
                    <div class="page__404__title">404</div>
                    <div class="page__404__text">
                        <p>Страница не найдена :(</p>
                        <p>Вероятно, Вы перешли по устаревшей или уже не работающей ссылке</p>
                        <p>Попробуйте воспользоваться поиском или <a href="<?= Url::home()?>" class="page__404__link">вернуться на главную</a></p>
                    </div>
                </div>
                <div class="page__404__img-block">
                    <div class="page__404__title-hidden">4<span>0</span>4</div>
                </div>
            </div>
        </div>
    </div>
<?php else:?>
    <div class="box" style="padding-top:1px">
        <div class="container">
            <h1 class="h1"><?php echo Html::encode($this->title) ?></h1>

            <p class="h3"><?php echo nl2br(Html::encode($message)) ?></p>

            <p>
                Вышеуказанная ошибка произошла, когда веб-сервер обрабатывал ваш запрос.
            </p>
            <p>
                Пожалуйста, свяжитесь с нами, если вы считаете, что это ошибка сервера. Спасибо.
            </p>

        </div>
    </div>
<?php endif;?>
