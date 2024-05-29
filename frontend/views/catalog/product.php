<?php
/* @var $this yii\web\View */
/* @var $productsBuy */
/* @var $productsCat */
/* @var $productsPopular */
/* @var $productsSale */

/* @var $model */

use cheatsheet\Time;
use frontend\widgets\common\seoH1\SeoH1;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\common\seoText\SeoText;
use yii\helpers\Html;
use frontend\widgets\products\row\ProductsRow;
use frontend\widgets\products\card\ProductCard;
use frontend\widgets\common\tabs\CommonTabs;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;
use frontend\widgets\common\question\CommonQuestion;
use frontend\widgets\common\review\CommonReview;
use frontend\helpers\All;

$seoTitle = $model->seo_title;
$seoDescription = $model->seo_desc;
$seoKeywords = $model->seo_keywords;
$seoH1 = $model->title;
$seoText = $model->bodyShort_;
$subdomainInfo = Yii::$app->subdomains->get();

echo SeoMetaTags::widget([
    'seoTitle' => $seoTitle,
    'seoDescription' => $seoDescription,
    'seoKeywords' => $seoKeywords,
    'subdomainInfo' => $subdomainInfo,
    'seoH1' => $seoH1,
]);

echo $this->registerMetaTag(['property' => 'og:title', 'content' => SeoH1::widget(['seoH1' => $seoH1, 'subdomainInfo' => $subdomainInfo])]);
echo $this->registerMetaTag(['property' => 'og:description', 'content' => SeoText::widget(['seoText' => $model->bodyShort_, 'subdomainInfo' => $subdomainInfo])]);
echo $this->registerMetaTag(['property' => 'og:image', 'content' => Url::to($model->getImgUrl())]);
echo $this->registerMetaTag(['property' => 'og:url', 'content' => Url::canonical()]);

$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => Url::to(['/catalog'])];

if ($model->category) {
    $this->params['breadcrumbs'][] = ['label' => SeoH1::widget(['seoH1' => $model->category->title, 'subdomainInfo' => $subdomainInfo]),
        'url' => Url::to(['/catalog/' . $model->category->slug])];
}

$this->params['breadcrumbs'][] = ['label' => SeoH1::widget(['seoH1' => $seoH1, 'subdomainInfo' => $subdomainInfo])];

$warranty = $model->warranty ? Html::tag('p',
    "Гарантия: ".All::asWarranty(floor($model->warranty * Time::SECONDS_IN_A_YEAR))
) : null;
$manufacturer = $model->manufacturer ? Html::tag('p', "Страна производителя: ".$model->manufacturer) : null;

$brand = $model->brand_ ? "<p>Бренд: <a href=\"".
    Url::to(['/brands/'.$model->brand_->slug])."\">". $model->brand_->title ."</a></p>" : null;

$this->params['body_encode'] = HtmlPurifier::process($model->body . $warranty . $manufacturer . $brand);
$this->params['video_code_encode'] = $model->video_code_;

?>

<div class="main-box">

    <section>
        <div class="container">
            <?= ProductCard::widget([
                'model' => $model,
                'reviews' => $model->pubReviews_,
            ]) ?>
        </div>
    </section>

    <section>
        <div class="box" style="padding-top: 10px;">
            <div class="container">
                <?= CommonTabs::widget([
                    'tabs' => [
                        [
                            'id' => "id__{$model->id}__desc",
                            'label' => 'Описание',
                            'active' => true,
                            'content' => "<div id='pr-desc' class='block-typo'>" . ( !empty($this->params['body_encode']) ?
                                $this->params['body_encode'] : 'нет описания' ) . "</div>"

                        ],
                        [
                            'id' => "id__{$model->id}__reviews",
                            'label' => 'Отзывы',
                            'content' => CommonReview::widget([
                                'reviews' => $model->pubReviews_,
                                'product_id' => $model->mainConfig->id,
                                'product_title' => $model->title,
                                'is_main_config' => $model->id == $model->mainConfig->id,
                            ])
                        ],
                        [
                            'id' => "id__{$model->id}__video",
                            'label' => 'Видео',
                            'disable' => empty($this->params['video_code_encode']),
                            'content' => "<div id='pr-video-iframe' class='video-iframe'>
                                {$this->params['video_code_encode']}</div>"
                        ],
                        [
                            'id' => "id__{$model->id}__qustion",
                            'label' => 'Вопрос-ответ',
                            'disable' => empty($model->pubQuestions_),
                            'content' => CommonQuestion::widget([
                               'questions' => $model->pubQuestions_
                            ])
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </section>


    <?php if ($productsBuy->getTotalCount()) { ?>
        <section>
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'С этим товаром также покупают',
                    'dataProvider' => $productsBuy
                ]) ?>
            </div>
        </section>
    <?php } ?>

    <?php if ($productsCat->getTotalCount()) { ?>
        <section>
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Аналогичные товары',
                    'dataProvider' => $productsCat
                ]) ?>
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

</div>
<br/>
