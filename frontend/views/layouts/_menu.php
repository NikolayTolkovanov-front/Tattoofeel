<?php

use common\models\ProductCategory;
use frontend\widgets\common\Icon;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use frontend\helpers\ForURL;
use yii\jui\AutoComplete;

$this->params['catalogs'] = ProductCategory::find()->published()->order()->all();
$keys = Yii::$app->keyStorageApp->getAll(['phone_2', 'head.link.telega', 'head.link.vk']);

$categoryMenu = \Yii::$app->CategoryList->getForMenu([
    'menuClass' => 'menu-nav__list__sub',
]);

?>

<div class="menu">
    <div class="container">
        <div class="menu-inner">
            <div class="menu__top__mob">
                <div>
                    <i class="fa fa-bars" aria-hidden="true"></i>
                    <div class="menu__top__mob__sandwich">
                        <?php if ('/' == Yii::$app->request->url):?>
                            <div>TATTOOFEEL.RU</div>
                        <?php else:?>
                            <a href="<?= Url::home() ?>">TATTOOFEEL.RU</a>
                        <?php endif;?>
                    </div>
                </div>
                <div class="menu__top__mob__btn">
                    <a href="#" class="icn-magnifier activate-search-mob"></a>
                    <?php if(Yii::$app->user->isGuest) {?>
                        <a href="<?= Url::to(['/lk/login']) ?>">Вход </a>
                    <?php } else { ?>
                        <a href="<?= Url::to(['/lk']) ?>">
                            <?= Icon::widget(['name' => 'lk','width'=>'20px','height'=>'20px',
                                'options'=>['fill'=>"#363636"]]) ?>
                            <span class="username__desktop"><?= Yii::$app->user->identity->username ?></span>
                        </a>
                    <?php } ?>
                    <a href="<?= Url::to(['/lk/cart']) ?>" style="position:relative">
                        <?= Icon::widget(['name' => 'cart','width'=>'24px','height'=>'24px','options'=>['fill'=>"#363636"]]) ?>
                        <div class="menu-cart__count"><?= Yii::$app->client->identity->cart->count ?></div>
                    </a>
                </div>
            </div>
            <div class="menu__logo">
                <?php if ('/' == Yii::$app->request->url):?>
                    <div href="<?= Url::home()?>" class="menu-logo">
                        <?= Icon::widget(['name' => 'logo-top','width'=>'90px','height'=>'90px']) ?>
                    </div>
                <?php else:?>
                    <a href="<?= Url::home()?>" class="menu-logo">
                        <?= Icon::widget(['name' => 'logo-top','width'=>'90px','height'=>'90px']) ?>
                    </a>
                <?php endif;?>
            </div>
            <div class="menu__right">
                <div class="menu__right__top">
                    <div class="menu__right__top__left">
                        <?php
                        $form = ActiveForm::begin([
                            'enableClientValidation' => false,
                            'enableAjaxValidation' => false,
                            'action' => Url::to(['/catalog/search']),
                            'method' => 'get',
                            'options' => [
                                'id' => 'header-menu-search',
                                'class' => 'menu-search'
                            ]
                        ]); ?>
                            <?= AutoComplete::widget([
                                'name' => 'q',
                                'value' => Yii::$app->request->get('q'),
                                'options' => [
                                    'placeholder' => "Название или код товара...",
                                    'id' => 'menu-search',
                                    'class' => 'placeholder'
                                ],
                                'clientOptions' => [
                                    'source' => Url::to(['/search']),
                                    'minLength' => '2',
                                    'autoFocus' => false,
                                    'classes' => [
                                        "ui-autocomplete" => "ui-autocomplete-search-menu"
                                    ]
                                ],
                            ]); ?>
                            <?= Icon::widget(['name' => 'loader','width'=>'18px','height'=>'18px',
                                'options'=>['class' => 'icon icon-loader']
                            ]) ?>

                            <input class="btn" type="submit" value="Найти" />
                            <button class="btn-mob" type="submit"></button>
                        <?php ActiveForm::end() ?>
                        <span class="lupa" style="position: absolute; right: 0px; z-index: 9999; background: rgb(255, 255, 255) none repeat scroll 0% 0%; top: 10px;width: 34px;height: 34px;display: none;justify-content: center;align-items: center;border-radius: 50%;cursor: pointer;"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                    <div class="menu__right__top__right">
                        <a href="<?= Url::to(['/lk/cart']) ?>" class="menu-cart">
                            <div class="menu-cart__icon"><?= Icon::widget(['name' => 'cart','width'=>'32px','height'=>'32px',
                                    'options'=>['fill'=>"#363636"]
                                ]) ?></div>
                            <div class="menu-cart__count"><?= Yii::$app->client->identity->cart->count ?></div>
                            <div class="menu-cart__sum <?= Yii::$app->client->identity->cart->sumFormat ? '' : 'none' ?>"
                                 id="cart-sum"><?= Yii::$app->client->identity->cart->sumFormat ?></div>
                        </a>
                    </div>
                </div>
                <div class="menu-nav">
                    <div class="container-DOWN-MD">
                        <ul class="menu-nav__list">
                            <li<?= ForURL::isActive('catalog')?>>
                                <a href="<?= Url::to(['/catalog']) ?>">Каталог</a>
                                <?= $categoryMenu ?>
                            </li>
                            <li<?= ForURL::isActive('article',null, 'menu-nav__item__hide-on-MD')?>>
                                <?php if (empty(ForURL::isActive('article'))):?>
                                    <a href="<?= Url::to(['/article']) ?>">Статьи</a>
                                <?php else:?>
                                    <span>Статьи</span>
                                <?php endif;?>
                            </li>
                            <li<?= ForURL::isActive('stock')?>>
                                <?php if (empty(ForURL::isActive('stock'))):?>
                                    <a href="<?= Url::to(['/stock']) ?>">Акции</a>
                                <?php else:?>
                                    <span>Акции</span>
                                <?php endif;?>
                            </li>

                            <li<?= ForURL::isActive('brands')?>>
                                <?php if (empty(ForURL::isActive('brands'))):?>
                                    <a href="<?= Url::to(['/brands']) ?>">Бренды</a>
                                <?php else:?>
                                    <span>Бренды</span>
                                <?php endif;?>
                            </li>
                            <li<?= ForURL::isActive('page','delivery', 'menu-nav__item__hide-on-MD')?>>
                                <?php if (empty(ForURL::isActive('page','delivery'))):?>
                                    <a href="<?= Url::to(['/delivery']) ?>">Доставка</a>
                                <?php else:?>
                                    <span>Доставка</span>
                                <?php endif;?>
                            </li>
                            <li<?= ForURL::isActive('page' ,'team')?>>
                                <?php if (empty(ForURL::isActive('page' ,'team'))):?>
                                    <a href="<?= Url::to(['/team']) ?>">Наша команда</a>
                                <?php else:?>
                                    <span>Наша команда</span>
                                <?php endif;?>
                            </li>
                            <li<?= ForURL::isActive('page', 'contact')?>>
                                <?php if (empty(ForURL::isActive('page' ,'contact'))):?>
                                    <a href="<?= Url::to(['/contact']) ?>">Контакты</a>
                                <?php else:?>
                                    <span>Контакты</span>
                                <?php endif;?>
                            </li>
                        </ul>
                        <div class="menu-nav__contact">
                            <div>
                                <div class="phone">
                                    <a href="tel:<?=Yii::$app->keyStorageApp->getPhoneValueEx(Yii::$app->params['subdomainInfo']['phone'])?>" class="phone__number">
                                        <?= Icon::widget(['name' => 'phone','width'=>'20px','height'=>'20px',
                                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                                        <?=Yii::$app->params['subdomainInfo']['phone']?>
                                    </a>
                                    <div class="phone__time"><?=Yii::$app->params['subdomainInfo']['work_time']?></div>
                                </div>
                            </div>
                            <div>
                                <div class="phone">
                                    <a href="tel:<?= Yii::$app->keyStorageApp->getPhoneValue('phone_2') ?>" class="phone__number">
                                        <?= Icon::widget(['name' => 'phone','width'=>'20px','height'=>'20px',
                                            'options'=>['fill'=>"#363636",'stroke'=>"#363636"]]) ?>
                                        <?= $keys['phone_2'] ?>
                                    </a>
                                    <div class="phone__time">Только звонки</div>
                                </div>
                            </div>
                            <div class="menu-nav__contact__soc">
                                <div>Мы в соцсетях</div>

                                <a target="_blank" href="<?= $keys['head.link.telega'] ?>">
                                    <?= Icon::widget(['name' => 'telega','width'=>'23px','height'=>'23px',
                                        'options'=>['fill'=>"#fff"]
                                    ]) ?>
                                </a>
                                <a target="_blank" href="<?= $keys['head.link.telega'] ?>">
                                    <?= Icon::widget(['name' => 'vk','width'=>'23px','height'=>'23px',
                                        'options'=>['fill'=>"#fff"]
                                    ]) ?>
                                </a>
                            </div>
                            <div class="footer__pay__img" style="margin: 25px 0 0 40px;filter: grayscale(100%);mix-blend-mode: difference;">
                                <a href="/pay-card/" class="img-master-visa"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<span class="mob-bg"></span>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<script>
    if($(window).width() < 422){
        $('.menu-nav__list').children().first().append("<p class='toogle__menu'><svg width='18' height='12' viewBox='0 0 18 12' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M2 2L9 10L16 2' stroke='#363636' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'/></svg></p>");
    }


    $(".toogle__menu").click(function(){
    	$(".menu-nav__list__sub").slideToggle(300);
    })



$(".toogle__menu").click(function(){
    $(".toogle__menu svg").toggleClass("caret_deg");
    }
)

$(".menu__top__mob i").click(function(){
    $(".menu-nav").toggleClass("open__mobile");
    $("body").toggleClass("-open-mob");
})

function opacity_t() {
  $(".menu-nav__contact").css("opacity", "1");
}


$(".menu__top__mob i").click(function(){
    if ($(".menu-nav").hasClass("open__mobile")) {
        $(".mob-bg").addClass("bg__mobile");

        $(".lupa").css("display", "flex");
        $(".menu-nav__contact").css("opacity", "0");
        opacity_t();
    } else {
			$(".mob-bg").removeClass("bg__mobile");

			$(".lupa").css("display", "none");
			$(".menu-nav__contact").css("opacity", "0");
	}
})


$(".lupa").click(function(){
    $(".-open-mob .menu").css("z-index", "9999");
        $(this).hide();
        $(".-open-mob .menu-nav").css("top", "102px");
    $(".menu-nav").removeClass("open__mobile");
    $(".menu-nav__contact").css("opacity", "0");
})

$(".mob-bg").click(function(){
    $(".menu-nav").removeClass("open__mobile");
        $(this).removeClass("bg__mobile");
        $(".menu-nav__contact").css("opacity", "0");
})

</script>
