<?php
/**
 * @var $productsRecently
 */

use frontend\widgets\products\row\ProductsRow;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Повторная отправка активационного письма';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Повторная отправка активационного письма'];
?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Повторная отправка активационного письма</h1>
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
                                <input name="ResendEmailForm[email]" type="text" class="ozn-inp" value="<?=$model->email?>" placeholder=" " />
                                <span class="label-text">Введите ваш email *</span>
                            </label>
                        </div>

                        <div class="lk-profile-col"></div>
                    </div>

                    <div class="lk-profile lk-profile-btns">
                        <div class="lk-profile-col">
                            <button type="submit" class="btn">Отправить</button>
                        </div>

                        <div class="lk-profile-col"></div>
                    </div>

                    <?php /*
                    <div class="lk-register-fields">
                        <input class="placeholder" name="ResendEmailForm[email]" type="text"
                               placeholder="email *" value="<?= $model->email ?>" />
                    </div>
                    */ ?>

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
