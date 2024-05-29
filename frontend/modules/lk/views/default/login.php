<?php
/**
 * @var $productsRecently
 */

use frontend\widgets\common\Icon;
use frontend\widgets\products\row\ProductsRow;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Вход';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Вход'];
?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Авторизация</h1>
                    <?php $form = ActiveForm::begin() ?>
                    <?php if( $model->hasErrors() ) { ?>
                        <div class="lk-register-smg">
                            <?php foreach($model->getErrors() as $ee)
                                foreach($ee as $e)
                                    echo "<p class='help-block-error'>{$e}</p>" ?>
                        </div>
                    <?php } ?>

                    <div class="lk-profile">
                        <div class="lk-profile-col">
                            <label class="like-ozn-labl">
                                <input name="LoginForm[identity]" type="text" class="ozn-inp" value="<?=$model->identity?>" placeholder=" " />
                                <span class="label-text">Логин или email *</span>
                            </label>

                            <label class="checkbox-f">
                                <input name="LoginForm[rememberMe]" type="checkbox" value="1" checked /><i></i>
                                Запомнить меня
                            </label>
                        </div>

                        <div class="lk-profile-col">
                            <label class="like-ozn-labl has-text">
                                <input name="LoginForm[password]" type="password" class="ozn-inp" value="<?=$model->password?>" placeholder=" " />
                                <span class="label-text">Пароль *</span>
                            </label>

                            <div class="right">
                                <a class="link-def_n" href="<?= Url::to(['/lk/remember']) ?>"><span>Забыли пароль</span></a>
                            </div>
                        </div>
                    </div>

                    <?php /*
                    <div class="lk-register-fields">
                        <input class="placeholder" name="LoginForm[identity]" type="text" placeholder="Логин или email *" value="<?= $model->identity ?>" />
                        <input class="placeholder" name="LoginForm[password]" type="password" placeholder="Пароль *" value="<?= $model->password ?>" />
                    </div>
                    */ ?>

<!--                    <input name="LoginForm[rememberMe]" type="hidden" value="0" />-->

                    <?php /*
                    <div class="lk-login-remember">
                        <div>
                            <label class="checkbox-f">
                                <input name="LoginForm[rememberMe]" type="checkbox" value="1" checked /><i></i>
                                Запомнить меня
                            </label>
                        </div>
                        <div class="right">
                            <a class="link-def_n" href="<?= Url::to(['/lk/remember']) ?>"><span>Забыли пароль</span></a>
                        </div>
                    </div>
                    */ ?>

                    <div class="lk-login-soc" style="display: none;">
                        <div class="lk-login-soc__title"><h3 class="h3">Войти, используя аккаунт соцсети:</h3></div>
                        <div class="lk-login-soc__btn">
                            <a href="#">
                                <?= Icon::widget(['name' => 'vk','width'=>'26px','height'=>'26px',
                                    'options'=>['fill'=>"#fff"]
                                ]) ?>
                            </a>
                            <a href="#">
                                <?= Icon::widget(['name' => 'fb','width'=>'26px','height'=>'26px',
                                    'options'=>['fill'=>"#fff"]
                                ]) ?>
                            </a>
                            <a href="#">
                                <?= Icon::widget(['name' => 'G','width'=>'26px','height'=>'26px',
                                    'options'=>['fill'=>"#fff"]
                                ]) ?>
                            </a>
                        </div>
                    </div>

                    <div class="lk-profile lk-profile-btns">
                        <div class="lk-profile-col">
                            <button type="submit" class="btn">Войти</button>

                            <?php if (Yii::$app->getModule('lk')->shouldBeActivated) : ?>
                                <p><?=
                                    Html::a('<span>Повторно отправить письмо активации</span>',
                                        ['/lk/resend'],
                                        ['class' => 'link-def_n']
                                    )?></p>
                            <?php endif; ?>
                        </div>

                        <div class="lk-profile-col">
                            <a href="<?= Url::to(['/lk/register']) ?>" class="btn">Регистрация</a>
                        </div>
                    </div>

                    <?php ActiveForm::end() ?>
                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
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