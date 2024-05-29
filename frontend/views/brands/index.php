<?php

/**
 * @var $mainModel common\models\Brand
 * @var $brandDataProvider common\models\Brand
 * @var $filterBrands common\models\Brand
 * @var $isAjax
 */

use frontend\widgets\categories\filter\CategoriesFilter;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\common\tlock\CommonTlock;

$seoTitle = $mainModel->seo_title;
$seoDescription = $mainModel->seo_desc;
$seoKeywords = $mainModel->seo_keywords;
$seoH1 = 'Бренды';
$subdomainInfo = Yii::$app->subdomains->get();

$this->params['breadcrumbs'][] = ['label' => $seoH1];
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

                <?php if ($mainModel):?>
                    <div style="min-height:222px;margin-bottom:-20px">
                        <?= CommonTlock::widget([
                            'title' => $mainModel->title,
                            'imgUrl' => $mainModel->getImgUrl(),
                            'body' => $mainModel->body,
                            'body_short' => $mainModel->body_short
                        ]) ?>
                    </div>
                <?php endif;?>

                <?= $this->render('_list', [
                    'brandDataProvider' => $brandDataProvider
                ]) ?>

            </section>
        </div>
    </div>
</div>

