<?php
/**
 * @var $dataProvider common\models\Stock
 * @var $productsRecently frontend\models\Product
 *
 */

use frontend\widgets\common\seoH1\SeoH1;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\products\row\ProductsRow;

$seoTitle = $model->seo_title;
$seoDescription = $model->seo_desc;
$seoKeywords = $model->seo_keywords;
$seoH1 = 'Акции';
$subdomainInfo = Yii::$app->subdomains->get();

$this->params['breadcrumbs'][] = ['label' => SeoH1::widget(['seoH1' => $seoH1, 'subdomainInfo' => $subdomainInfo])];
?>

<?=SeoMetaTags::widget([
    'seoTitle' => $seoTitle,
    'seoDescription' => $seoDescription,
    'seoKeywords' => $seoKeywords,
    'subdomainInfo' => $subdomainInfo,
    'seoH1' => $seoH1,
])?>

<section>
    <div class="box no-padding-top">
        <div class="container">
            <h1 class="h1">
                <?=SeoH1::widget([
                    'seoH1' => $seoH1,
                    'subdomainInfo' => $subdomainInfo,
                ])?>
            </h1>
            <?= $this->render('_list',[
                'dataProvider' => $dataProvider
            ]) ?>
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
