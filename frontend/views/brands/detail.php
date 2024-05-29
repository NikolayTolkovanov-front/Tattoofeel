<?php

/**
 * @var $mainModel common\models\Brand
 * @var $brandDataProvider common\models\Brand
 * @var $filterBrands common\models\Brand
 * @var $productDataProvider frontend\models\Product
 * @var $isAjax
 */

use frontend\widgets\categories\filter\CategoriesFilter;
use frontend\widgets\common\seoH1\SeoH1;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\common\tlock\CommonTlock;
use frontend\widgets\products\plist\ProductsList;
use yii\helpers\Url;

$seoTitle = $mainModel->seo_title;
$seoDescription = $mainModel->seo_desc;
$seoKeywords = $mainModel->seo_keywords;
$seoH1 = $mainModel->title;
$subdomainInfo = Yii::$app->subdomains->get();

$this->params['breadcrumbs'][] = ['label' => 'Бренды', 'url' => Url::to(['/brands'])];
$this->params['breadcrumbs'][] = ['label' => SeoH1::widget(['seoH1' => $seoH1, 'subdomainInfo' => $subdomainInfo])];
?>

<?=SeoMetaTags::widget([
    'seoTitle' => $seoTitle,
    'seoDescription' => $seoDescription,
    'seoKeywords' => $seoKeywords,
    'subdomainInfo' => $subdomainInfo,
    'seoH1' => $seoH1,
])?>

<div class="box" style="padding-top:20px">
    <div class="container">
        <div class="grid-left-col">
            <aside class="grid-left-col__aside">
                <?= CategoriesFilter::widget(['brand' => true, 'filterBrands' => $filterBrands]) ?>
            </aside>
            <section class="grid-left-col__main">

                <div style="min-height:238px;">
                    <?= CommonTlock::widget([
                        'title' => $mainModel->title,
                        'imgUrl' => $mainModel->getImgUrl(),
                        'body' => $mainModel->body,
                        'body_short' => $mainModel->body_short
                    ]) ?>
                </div>

                <?= ProductsList::widget([
                    'dataProvider' => $productDataProvider,
                    'linkLoadMore' => Url::to(['/brands/'.$mainModel->slug]),
                    'emptyListShow' => false,
                    'brandPage' => true,
                ]) ?>
            </section>
        </div>
    </div>
</div>
