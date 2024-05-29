<?php
/**
 * @var $productsRecently
 */

use frontend\widgets\common\Icon;
use frontend\widgets\products\row\ProductsRow;
use yii\helpers\Url;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Любимые товары';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Любимые товары'];
?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Любимые товары</h1>
                    <p>Хочешь первым получать информацию о новых поступлениях нужных тебе товаров, акциях и скидках  или просто знать, когда товара остается на складе мало?</p>
                    <p>Тогда добавь товар в любимые.</p>
                    <p>Выбери тип уведомлениий о любимых товарах:</p>
                    <p>
                        <label class="checkbox" style="display:inline-block">
                            <input name="def" type="checkbox" checked />
                            <i></i> Акции и скидки</label> &nbsp;&nbsp;&nbsp;
                        <label class="checkbox" style="display:inline-block">
                            <input  name="def" type="checkbox" />
                            <i></i> Новое поступление</label> &nbsp;&nbsp;&nbsp;
                        <label class="checkbox" style="display:inline-block">
                            <input  name="def" type="checkbox" />
                            <i></i> Товар заканчивается на складе</label>
                    </p>
                    <div class="lk-table-wrap">
                        <div class="lk-table">
                            <?php if(!empty($deferred)) { ?>
                            <div class="lk-table__head">
                                <div class="lk-table-fd-name">Наименование товара:</div>
                                <div class="lk-table-fd-stock">Наличие на складе:</div>
                                <div class="lk-table-fd-buy"></div>
                                <div class="lk-table-fd-del"></div>
                            </div>
                            <?php } ?>
                            <?php if(!empty($deferred)) { ?>
                                <?php foreach($deferred as $d)
                                        echo $this->render('_deferred_item',['model' => $d]);
                                    ?>
                            <?php } else { ?>
                                <div class="lk-table__row">
                                    <div class="lk-table-fd-name _empty">Нет любимых товаров</div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
        </div>
    </div>
</section>



<?php if($productsRecently->getTotalCount()) { ?>
    <section>
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
