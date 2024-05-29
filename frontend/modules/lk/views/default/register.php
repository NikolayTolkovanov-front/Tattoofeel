<?php
/**
 * @var $productsRecently
 */
use frontend\widgets\products\row\ProductsRow;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Регистрация';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Регистрация'];
?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Регистрация</h1>
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
                                <input name="SignupForm[username]" type="text" class="ozn-inp" value="<?=$model->username?>" placeholder=" " />
                                <span class="label-text">Логин *</span>
                            </label>

                            <label class="like-ozn-labl">
                                <input name="SignupForm[phone]" type="text" class="ozn-inp" value="<?=$model->phone?>" placeholder=" " />
                                <span class="label-text">Телефон *</span>
                            </label>

                            <label class="like-ozn-labl">
                                <input name="SignupForm[password]" type="password" class="ozn-inp" placeholder=" " />
                                <span class="label-text">Пароль *</span>
                            </label>
                        </div>

                        <div class="lk-profile-col">
                            <label class="like-ozn-labl">
                                <input name="SignupForm[full_name]" type="text" class="ozn-inp" value="<?=$model->full_name?>" placeholder=" " />
                                <span class="label-text">ФИО *</span>
                            </label>

                            <label class="like-ozn-labl">
                                <input name="SignupForm[email]" type="text" class="ozn-inp" value="<?=$model->email?>" placeholder=" " />
                                <span class="label-text">Email *</span>
                            </label>

                            <label class="like-ozn-labl">
                                <input name="SignupForm[password_confirm]" type="password" class="ozn-inp" placeholder=" " />
                                <span class="label-text">Повторить пароль *</span>
                            </label>
                        </div>
                    </div>

                    <?php /*
                    <div class="lk-register-fields">
                        <input name="SignupForm[username]" type="text" placeholder="Логин *" class="placeholder" value="<?= $model->username ?>" />
                        <input name="SignupForm[full_name]" type="text" placeholder="ФИО *" class="placeholder" value="<?= $model->full_name ?>" />
                        <input name="SignupForm[phone]" type="text" placeholder="Телефон *" class="placeholder" value="<?= $model->phone ?>" />
                        <input name="SignupForm[email]"type="text" placeholder="Email *" class="placeholder" value="<?= $model->email ?>" data-inputmask="'alias': 'email_cr'" />
                        <input name="SignupForm[password]" type="password" placeholder="Пароль *" class="placeholder" />
                        <input name="SignupForm[password_confirm]" type="password" placeholder="Повторить пароль *" class="placeholder" />
                    </div>
                    */ ?>

                    <input type="hidden" name="SignupForm[offers]" value="0" />
                    <div class="lk-profile lk-profile-btns">
                        <div class="lk-profile-col">
                            <label class="checkbox-f">
                                <input type="checkbox" name="SignupForm[offers]" value="1" /><i></i>
                                Согласен с условиями <a href="<?= Url::to(['/offers']) ?>">Публичной оферты</a>
                            </label>
                        </div>
                        <div class="lk-profile-col">
                            <button type="submit" class="btn _big">Регистрация</button>
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

