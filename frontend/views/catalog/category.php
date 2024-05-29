<?php
/* @var $this yii\web\View */
/* @var $category common\models\ProductCategory */
/* @var $productDataProvider frontend\models\Product */
/* @var $minMaxPrices array */
/* @var $productsPopular */
/* @var $discount */
/* @var $arFilter */
/* @var $metaTags array */

use yii\helpers\Url;
use common\models\ProductCategory;
use frontend\widgets\categories\filter\CategoriesFilter;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\common\seoH1\SeoH1;
use frontend\widgets\common\seoText\SeoText;
use frontend\widgets\products\plist\ProductsList;

$seoUrl = $metaTags['url'] ?? null;
$seoTitle = $metaTags['title'] ?? $category->seo_title;
$seoDescription = $metaTags['description'] ?? $category->seo_desc;
$seoKeywords = $metaTags['keywords'] ?? $category->seo_keywords;

$seoH1 = !empty($metaTags['h1']) ? $metaTags['h1'] : $category->title;
$seoText = !empty($metaTags['seo_text']) ? $metaTags['seo_text'] : $category->body_short;
$subdomainInfo = Yii::$app->subdomains->get();

if (isset($arFilter['q'])) {
    $seoH1 = 'Поиск';
} elseif ($discount) {
    $seoH1 = 'Товары со скидкой';
}

$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => Url::to(['/catalog'])];

if (!isset($arFilter['q'])) {
    $breadcrumbs = \Yii::$app->CategoryList->getBreadCrumbs($category);
    if ($breadcrumbs) {
        $slug = '';
        foreach ($breadcrumbs as $bc) {
            $slug .= $bc['slug'] .'/';
            $nestedCategoryMarker = $bc['parent_id'] ? ProductCategory::LAST_NESTED_SLUG_MARKER . '/' : '';
            $this->params['breadcrumbs'][] = [
                'url' => Url::to('/catalog/' . $slug . $nestedCategoryMarker),
                'label' => SeoH1::widget([
                    'seoH1' => $bc['title'],
                    'subdomainInfo' => $subdomainInfo
                ])
            ];
        }
    }
}

$this->params['breadcrumbs'][] = ['label' => SeoH1::widget(['seoH1' => $seoH1, 'subdomainInfo' => $subdomainInfo])];
$this->params['breadcrumbsClass'] = '_short';
?>

<?=SeoMetaTags::widget([
    'seoUrl' => $seoUrl,
    'seoTitle' => $seoTitle,
    'seoDescription' => $seoDescription,
    'seoKeywords' => $seoKeywords,
    'subdomainInfo' => $subdomainInfo,
    'seoH1' => $seoH1,
])?>

<div class="box" style="padding-top:20px">
    <div class="container">
        <h1 class="visually-hidden">
            <?=SeoH1::widget([
                'seoH1' => $seoH1,
                'subdomainInfo' => $subdomainInfo,
            ])?>
        </h1>
        <div class="grid-left-col">
            <aside class="grid-left-col__aside">
                <?= CategoriesFilter::widget([
                    'category' => $category,
                    'minMaxPrices' => $minMaxPrices,
                    'arFilter' => $arFilter,
                    'search' => isset($arFilter['q']),
                    'discount' => $discount,
                ]) ?>
            </aside>

            <p class="title__mob" style="font-style: normal;font-weight: normal;font-size: 20px;line-height: 24px;color: #363636;display: none;"><?= $catTitle ?></p>
            <section class="grid-left-col__main" id="product-list-container">
                <?= ProductsList::widget([
                    'dataProvider' => $productDataProvider,
                    'linkLoadMore' => isset($category->slug) ? Url::to(['/catalog/'.$category->slug]) : Url::to(['/catalog/']),
                    //'sorted' => true,
                    'sorted' => $arFilter['sorted']['TYPE'] && $arFilter['sorted']['ORDER'] ? $arFilter['sorted']['TYPE'] . '-' . $arFilter['sorted']['ORDER'] : false,
                    //'inStock' => true
                    'inStock' => $arFilter['inStock'] == 1 ? true : false,
                    'pagePost' => $arFilter['pagePost'] - 1,
                ]) ?>
            </section>
        </div>
    </div>
</div>

<?php /*if ($productsPopular->getTotalCount()) { ?>
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
<?php } */?>

<?php if (!empty($seoText)):?>
    <div class="category-footer">
        <div class="container">
            <div class="category-footer-wrapper">
                <div class="category-footer-title">
                    <?=SeoH1::widget([
                        'seoH1' => $seoH1,
                        'subdomainInfo' => $subdomainInfo,
                    ])?>
                </div>
                <div class="category-footer-desc">
                    <div class="category-footer-desc-text">
                        <?=SeoText::widget([
                            'seoText' => $seoText,
                            'subdomainInfo' => $subdomainInfo,
                        ])?>
                    </div>
                    <div class="category-footer-btn">
                        <a href="#" class="btn _wide btn-more">Читать далее</a>
                        <a href="#" class="btn _wide btn-less">Свернуть</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>
