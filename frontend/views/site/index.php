<?php
/* @var $this yii\web\View */
/* @var $productsNew */
/* @var $productsSale */
/* @var $productsPopular */
/* @var $categoryDataProvider */
/* @var $brandDataProvider */
/* @var $mainBannerDataProvider */
/* @var $teamWidget */

use frontend\controllers\CatalogController;
use frontend\widgets\categories\tile\CategoriesTile;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\home\slider\HomeSlider;
use frontend\widgets\home\team\HomeTeam;
use frontend\widgets\home\logos\HomeLogos;
use frontend\widgets\products\row\ProductsRow;
use yii\helpers\Url;

$metaTags = CatalogController::getSeoMetaTags('/');
echo SeoMetaTags::widget([
    'seoTitle' => $metaTags['title'],
    'seoDescription' => $metaTags['description'],
    'seoKeywords' => $metaTags['keywords'],
    'subdomainInfo' => Yii::$app->subdomains->get(),
]);
?>

<h1 class="visually-hidden"><?=$metaTags['h1']?></h1>

<?php if($mainBannerDataProvider->getTotalCount()) { ?>
    <section>
        <div class="box-gray no-padding">
            <div class="container-DOWN-MD home-slider-box-mob">
                <?= HomeSlider::widget([
                    'dataProvider' => $mainBannerDataProvider
                ]) ?>
            </div>
        </div>
    </section>
<?php } ?>

<section>
    <div class="box-gray page-home--box-category">
        <div class="container  page-home--box-category__container">
            <?= CategoriesTile::widget([
                'linkShowAll' => Url::to(['/catalog']),
                'dataProvider' => $categoryDataProvider,
                'head' => 'Каталог'
            ]) ?>
        </div>
        <div class="more__cat" style="display:none"><span>Показать еще</span></div>
    </div>
</section>

<?php if($productsSale->getTotalCount()) { ?>
    <section>
        <div class="box-gray">
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Товары по акции',
                    'dataProvider' => $productsSale
                ])?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if($productsNew->getTotalCount()) { ?>
    <section>
        <div class="box-gray">
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Новинки',
                    'dataProvider' => $productsNew
                ])?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if($productsPopular->getTotalCount()) { ?>
    <section>
        <div class="box-gray">
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Популярные товары',
                    'dataProvider' => $productsPopular
                ])?>
            </div>
        </div>
    </section>
<?php } ?>

<?php /* if($teamWidget) { ?>
    <section>
        <div class="box-white">
            <div class="container">
                <?= HomeTeam::widget(['model' => $teamWidget]) ?>
            </div>
        </div>
    </section>
<?php } */ ?>

<?php if($brandDataProvider->getTotalCount()) { ?>
    <section>
        <div class="box-white">
            <div class="container container-slider-row">
                <?= HomeLogos::widget([
                        'dataProvider' => $brandDataProvider
                ]) ?>
            </div>
        </div>
    </section>
<?php } ?>

<script>
    $("body").on('click', '.more__cat span', function(){
        $("#w0__list a").show();
        $(this).hide();
    })
</script>
