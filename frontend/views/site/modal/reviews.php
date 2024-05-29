<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */
?>


<div class="modal-close"></div>
<h2 class="h2 center">Предложения<br>и отзывы</h2>

<?php $form = ActiveForm::begin(['action' => '/send-review/', 'options' => ['class' => 'reviews-form']]) ?>
<p class='help-block-error'></p>

<div class="contact-form__fields">
    <?=$form->field($model, 'name')->textInput(['placeholder' => 'Иван Иванов', 'class' => 'placeholder'])->label('Имя:*')?>
    <?=$form->field($model, 'phone')->textInput(['placeholder' => '+7 (950) 60 78 030', 'class' => 'placeholder', 'data-inputmask' => "'mask': '+7 (999) 999-99-99'"])->label('Телефон:*')?>
    <?=$form->field($model, 'body')->textArea(['rows' => 6, 'placeholder' => 'Ваш текст', 'class' => 'placeholder'])->label('Отзыв:*')?>
</div>
<div class="offer-row">
    <a target="_blank" href="/offers/" class="offer">Оферта</a>
    <div class="contact-form__btn">
        <?=Html::submitButton('Отправить', ['class' => 'btn _big', 'name' => 'contact-button'])?>
    </div>
</div>

<?php ActiveForm::end() ?>
