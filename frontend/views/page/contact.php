<?php
/* @var $this yii\web\View */
/* @var $model */
/* @var $productsRecently */

use frontend\widgets\common\Icon;
use frontend\widgets\common\seoH1\SeoH1;
use frontend\widgets\common\seoMetaTags\SeoMetaTags;
use frontend\widgets\products\row\ProductsRow;
use yii\bootstrap\ActiveForm;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;

$seoTitle = $model->seo_title;
$seoDescription = $model->seo_desc;
$seoKeywords = $model->seo_keywords;
$seoH1 = $model->title;
$subdomainInfo = Yii::$app->subdomains->get();

$this->params['breadcrumbs'][] = ['label' => SeoH1::widget(['seoH1' => $seoH1, 'subdomainInfo' => $subdomainInfo])];

$this->registerJsFile('https://api-maps.yandex.ru/2.1/?apikey=a0918dc8-0aa3-4828-8296-38ab02f88c79&lang=ru_RU');
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
        <div class="static-page contact" itemscope="" itemtype="http://schema.org/Organization">
            <div class="static-page__grid">
                <p style="display: none;" itemprop="name">Tattoofeel.ru</p>
                <div class="static-page__grid__head">
                    <h2 class="h1">
                        <?=SeoH1::widget([
                            'seoH1' => $seoH1,
                            'subdomainInfo' => $subdomainInfo,
                        ])?>
                    </h2>
                </div>
                <?php if (!$subdomainInfo['id']):?>
                    <div class="static-page__grid__photo _no-dot-grid _map">
                        <div class="static-page__grid__photo__map">
                            <?= Yii::$app->keyStorageApp->get('map') ?>
                        </div>
                    </div>
                <?php endif;?>
                <div class="static-page__grid__desc block-typo">
                    <?php /*
                    <div class="contact__block-data cb_addr">
                        <?= Icon::widget(['name' => 'point','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <div class="contact__block-data__head">Адрес:</div>
                        <div class="contact__block-data__desc" itemprop="address"><?= Yii::$app->keyStorageApp->get('address') ?></div>
                    </div>
                    <div class="contact__block-data cb_time">
                        <?= Icon::widget(['name' => 'time','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <div class="contact__block-data__head">График работы:</div>
                        <div class="contact__block-data__desc" itemprop="schedule"><?= Yii::$app->keyStorageApp->get('time') ?></div>
                    </div>
                    <div class="contact__block-data cb_phone">
                        <?= Icon::widget(['name' => 'phone','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <div class="contact__block-data__head">Телефоны:</div>
                        <div class="contact__block-data__desc" itemprop="telephone">
                            <a href="tel:<?=Yii::$app->keyStorageApp->getPhoneValue('phone_1')?>" onclick="ym(73251517,'reachGoal','phone_contact'); return true;"><?=Yii::$app->keyStorageApp->get('phone_1')?></a><br />
                        </div>
                    </div>
                    */ ?>
                    <div class="contact__block-data cb_addr">
                        <?= Icon::widget(['name' => 'point','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <div class="contact__block-data__head">Адрес:</div>
                        <div class="contact__block-data__desc" itemprop="address"><?=$subdomainInfo['address']?></div>
                    </div>
                    <?php if ($subdomainInfo['work_hours_showroom']) { ?>
                        <div class="contact__block-data cb_time">
                            <?= Icon::widget(['name' => 'time','width'=>'20px','height'=>'20px',
                                'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                            <div class="contact__block-data__head">График работы Шоурума:</div>
                            <div class="contact__block-data__desc" itemprop="schedule"><?=$subdomainInfo['work_hours_showroom']?></div>
                        </div>
                    <?php } ?>
                    <div class="contact__block-data cb_time">
                        <?= Icon::widget(['name' => 'time','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <div class="contact__block-data__head">График работы интернет-магазина:</div>
                        <div class="contact__block-data__desc" itemprop="schedule"><?=$subdomainInfo['work_time']?></div>
                    </div>
                    <div class="contact__block-data cb_phone">
                        <?= Icon::widget(['name' => 'phone','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <div class="contact__block-data__head">Телефоны:</div>
                        <div class="contact__block-data__desc" itemprop="telephone">
                            <?= Html::a($subdomainInfo['phone'], 'tel:'.Yii::$app->keyStorageApp->getPhoneValueEx($subdomainInfo['phone'])) ?><br />
                        </div>
                    </div>
                    <div class="contact__block-data">
                        <?= Icon::widget(['name' => 'mail','width'=>'20px','height'=>'20px',
                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                        <div class="contact__block-data__head">Email:</div>
                        <div class="contact__block-data__desc" itemprop="email">
                            <?= Html::mailto(
                                    Yii::$app->keyStorageApp->get('email'),
                                    Yii::$app->keyStorageApp->get('email')
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="contact__bottom">
            <div class="contact__bottom-desc block-typo">
                <?= HtmlPurifier::process($model->body)  ?>
                <?php /*
                <div class="contact__cert">
                    <div class="contact__cert__item">
                        <div><img src="/img/cert1.png" alt="Свидетельсвто о регистрации" /></div>
                        <span>Свидетельсвто о регистрации</span>
                    </div>
                    <div class="contact__cert__item">
                        <div><img src="/img/cert2.png" alt="Регистрация в налоговом органе" /></div>
                        <span>Регистрация в налоговом органе</span>
                    </div>
                </div>
                */ ?>
            </div>
            <div class="contact__bottom-form">
                <div class="contact-form">
                    <h2 class="h2 center">Написать нам</h2>
                    <?php $form1 = ActiveForm::begin() ?>
                    <?php if( $form->hasErrors() ) { ?>
                        <div class="lk-register-smg">
                            <?php foreach($form->getErrors() as $ee)
                                foreach($ee as $e)
                                    echo "<p class='help-block-error'>{$e}</p>" ?>
                        </div>
                    <?php } ?>
                        <div class="contact-form__fields">
<input name="ContactForm[name]" type="text" placeholder="Имя *" class="placeholder" value="<?= $form->name ?>" />
<input name="ContactForm[email]" type="text" placeholder="Email *" class="placeholder" value="<?= $form->email ?>"  data-inputmask="'alias': 'email_cr'" />
<input name="ContactForm[phone]" type="text" placeholder="Телефон *" class="placeholder" value="<?= $form->phone ?>"  data-inputmask-regexp="'mask': '+7 (999) 999-99-99'" />
<textarea name="ContactForm[body]" placeholder="Сообщение *" class="placeholder"><?= $form->body ?></textarea>
                        </div>

                        <div class="offer-row">
                            <a target="_blank" href="/offers/" class="offer">Оферта</a>
                            <div class="contact-form__btn"><button type="submit" class="btn _big">Отправить</button></div>
                        </div>
                    <?php ActiveForm::end() ?>
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
