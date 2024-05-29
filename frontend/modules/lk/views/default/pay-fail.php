<?php
/**
 * @var $productsRecently
 */
use frontend\widgets\products\row\ProductsRow;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Корзина | Неудачная оплата';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Корзина', 'url' => Url::to(['/lk/cart'])];
$this->params['breadcrumbs'][] = ['label' => 'Неудачная оплата'];

?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <!--<h1 class="h3">Оплата прошла успешно</h1>-->
                    <h1 class="h3">Неудачная оплата!</h1>
                    <a href="<?= Url::to(['/lk/pay']) ?>" class="btn _big">Попробовать снова</a>
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
