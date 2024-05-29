<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */
?>


<div class="modal-close"></div>
<h2 class="h2 center">Купить<br>в один клик</h2>

<?php $form = ActiveForm::begin(['action' => '/send-buy-one-click/', 'options' => ['class' => 'buy-one-click-form']]) ?>
<p class='help-block-error'></p>

<div class="contact-form__fields">
    <?=$form->field($model, 'link')->hiddenInput(['value' => $_SERVER['HTTP_REFERER']])->label(false)?>
    <?=$form->field($model, 'name')->textInput(['placeholder' => 'Иван Иванов', 'class' => 'placeholder'])->label('Имя:*')?>
    <?=$form->field($model, 'phone')->textInput(['placeholder' => '+7 (950) 60 78 030', 'class' => 'placeholder', 'data-inputmask' => "'mask': '+7 (999) 999-99-99'"])->label('Телефон:*')?>
</div>

<div class="offer-row">
    <a target="_blank" href="/offers/" class="offer">Оферта</a>
    <div class="contact-form__btn">
        <button type="submit" class="btn _big" name="contact-button" onclick="ym(73251517,'reachGoal','one_click_submit'); return true;">Отправить</button>
    </div>
</div>

<?php ActiveForm::end() ?>
