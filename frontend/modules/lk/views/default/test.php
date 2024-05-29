<?php
/**
 * @var $resp
 * @var $pos
 * @var $order
 */

use yii\helpers\Url;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Тестовая страница';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Тестовая страница'];
//echo '<pre>';print_r($_SERVER);echo '</pre>';
?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Тестовая страница</h1>

                    <pre><?php print_r($resp);?></pre>
                    <pre><?php //print_r($pos);?></pre>
                    <pre><?php //print_r($order);?></pre>

                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
        </div>
    </div>
</section>
