<?php
/* @var $this yii\web\View */
/* @var $category common\models\ProductCategory */
/* @var $productDataProvider frontend\models\Product */
/* @var $minMaxPrices array */
/* @var $productsPopular */

use frontend\widgets\products\row\ProductsRow;
use yii\helpers\Url;
use frontend\widgets\categories\filter\CategoriesFilter;
use frontend\widgets\products\plist\ProductsList;

$this->title = $category->seo_title ?: Yii::$app->params['title'];

//if ($category->seo_desc) {
//    global $has_seo_desc;
//    $has_seo_desc = true;
//    $this->registerMetaTag(['name' => 'description', 'content' => $category->seo_desc ?: Yii::$app->params['description']]);
//    $this->registerMetaTag(['name' => 'keywords', 'content' => $category->seo_keywords ?: Yii::$app->params['keywords']]);
//}

$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => Url::to(['/catalog'])];
$this->params['breadcrumbs'][] = ['label' => 'Товары со скидкой'];
$this->params['breadcrumbsClass'] = '_short';

?>

<div class="box" style="padding-top:20px">
    <div class="container">
        <div class="grid-left-col">
            <aside class="grid-left-col__aside">
                <?= CategoriesFilter::widget([
                    'category' => $category,
                    'minMaxPrices' => $minMaxPrices,
                    'discount' => true,
                ])?>
            </aside>

            <p class="title__mob" style="font-style: normal;font-weight: normal;font-size: 20px;line-height: 24px;color: #363636;display: none;">Товары со скидкой</p>
            <section class="grid-left-col__main" id="product-list-container">
                <?= ProductsList::widget([
                    'dataProvider' => $productDataProvider,
                    'linkLoadMore' => Url::to(['/catalog/discount']),
                    'sorted' => true,
                    'inStock' => true,
                ]) ?>
            </section>
        </div>
    </div>
</div>

<?php /* if ($productsPopular->getTotalCount()) { ?>
    <section>
        <div class="box-gray">
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Популярные товары',
                    //'dataProvider' => $productDataProvider,
                    'dataProvider' => $productsPopular
                ])?>
            </div>
        </div>
    </section>
<?php } */?>