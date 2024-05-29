<?php
/* @var $this yii\web\View */
/* @var $model */
/* @var $productsRecently */

use frontend\widgets\common\seoH1\SeoH1;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\products\row\ProductsRow;
use yii\helpers\HtmlPurifier;

$seoTitle = $model->seo_title;
$seoDescription = $model->seo_desc;
$seoKeywords = $model->seo_keywords;
$seoH1 = $model->title;
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
    <div class="container" style="margin-top:25px">
        <div class="static-page <?= $model->hasImg() ?'': ' _no-pict' ?>">
            <div class="static-page__grid <?= $model->hasImg() && $model->thumbnail_desc ? '_has_photo_desc' : '' ?>">
                <div class="static-page__grid__head">
                    <h2 class="h1">
                        <?=SeoH1::widget([
                            'seoH1' => $seoH1,
                            'subdomainInfo' => $subdomainInfo,
                        ])?>
                    </h2>
                </div>
                <?php if($model->hasImg()) { ?>
                    <div class="static-page__grid__photo">
                        <div class="static-page__grid__photo__pict">
                            <div style="background-image:url(<?= $model->getImgUrl() ?>)"></div>
                        </div>
                        <?php if(!empty(strip_tags($model->thumbnail_desc))) { ?>
                        <div class="static-page__grid__photo__desc block-typo">
                            <?= HtmlPurifier::process($model->thumbnail_desc)  ?>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="static-page__grid__desc block-typo">
                    <?= HtmlPurifier::process($model->body)  ?>
                </div>
            </div>
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
