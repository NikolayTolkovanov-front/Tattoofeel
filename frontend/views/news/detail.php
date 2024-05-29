<?php
/* @var $this yii\web\View
 * @var $productsRecently frontend\models\Product
 */

use frontend\widgets\common\seoH1\SeoH1;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\products\row\ProductsRow;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;

$seoTitle = $model->seo_title;
$seoDescription = $model->seo_desc;
$seoKeywords = $model->seo_keywords;
$seoH1 = $model->title;
$subdomainInfo = Yii::$app->subdomains->get();

$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => Url::to(['/news'])];
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
    <div class="container" style="margin-top:25px">
        <div class="static-page <?= $model->hasImg() ?'': ' _no-pict' ?>">
            <div class="static-page__grid">
                <div class="static-page__grid__head">
                    <h2 class="h2">
                        <?=SeoH1::widget([
                            'seoH1' => $seoH1,
                            'subdomainInfo' => $subdomainInfo,
                        ])?>
                    </h2>
                    <span><?= Yii::$app->formatter->asDate($model->published_at) ?></span>
                </div>
                <?php if($model->hasImg()) { ?>
                    <div class="static-page__grid__photo">
                        <div class="static-page__grid__photo__pict">
                            <div style="background-image:url(<?= $model->getImgUrl() ?>)"></div>
                        </div>
                    </div>
                <?php } ?>
                <div class="static-page__grid__desc block-typo">
                    <?= HtmlPurifier::process($model->body)?>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="box-white">
        <?php if($productsRecently->getTotalCount()) { ?>
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Популярные товары',
                    'dataProvider' => $productsRecently
                ])?>
            </div>
        <?php } ?>
    </div>
</section>
