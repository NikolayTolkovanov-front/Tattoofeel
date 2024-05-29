<?php
/**
 * @var $productsNew
 * @var $productsSale
 * @var $productsPopular
 * @var $dataProvider
 * @var $this yii\web\View
 */

use frontend\controllers\CatalogController;
use frontend\widgets\categories\tile\CategoriesTile;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\products\row\ProductsRow;

$metaTags = CatalogController::getSeoMetaTags('/catalog/');
echo SeoMetaTags::widget([
    'seoTitle' => $metaTags['title'],
    'seoDescription' => $metaTags['description'],
    'seoKeywords' => $metaTags['keywords'],
    'subdomainInfo' => Yii::$app->subdomains->get(),
]);

$this->params['breadcrumbs'][] = ['label' => 'Каталог'];
?>

<section class="page-catalog--sec-category">
    <div class="container">
        <?= CategoriesTile::widget([
            'linkShowMore' => true,
            'dataProvider' => $dataProvider,
            'head' => $metaTags['h1'],
        ]) ?>
    </div>
</section>

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

<?php if($productsSale->getTotalCount()) { ?>
    <section>
        <div class="box-white">
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Товары по акции',
                    'dataProvider' => $productsSale
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

