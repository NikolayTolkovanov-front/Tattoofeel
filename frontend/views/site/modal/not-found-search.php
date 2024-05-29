<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */
?>


<div class="modal-close"></div>
<h2 class="h2 center">Не нашли,<br>что искали?</h2>

<?php $form = ActiveForm::begin(['action' => '/send-not-found-search/', 'options' => ['class' => 'not-found-search-form']]) ?>
<p class='help-block-error'></p>

<div class="contact-form__fields">
    <?=$form->field($model, 'name')->textInput(['placeholder' => 'Иван Иванов', 'class' => 'placeholder'])->label('Имя:*')?>
    <?=$form->field($model, 'phone')->textInput(['placeholder' => '+7 (950) 60 78 030', 'class' => 'placeholder', 'data-inputmask' => "'mask': '+7 (999) 999-99-99'"])->label('Телефон:*')?>
    <?=$form->field($model, 'product')->textInput(['placeholder' => 'Название товара', 'class' => 'placeholder'])->label('Название товара:*')?>
</div>

<div class="contact-form__btn">
    <?=Html::submitButton('Отправить', ['class' => 'btn _big', 'name' => 'contact-button'])?>
</div>

<?php ActiveForm::end() ?>
