<?php

?>
<br />
<div class="row">
    <div class="col-xs-8">
        <div id="seo-snippet" class="seo-snippet">
            <a href="<?=Yii::$app->request->hostInfo."/catalog/{$model->category->slug}/{$model->slug}/"?>" target="_blank" class="seo-snippet-link">
                <h3 id="seo-snippet-title" class="seo-snippet-title"><?=$model->seo_title ?: 'Seo Заголовок'?></h3>
                <span id="seo-snippet-url" class="seo-snippet-url"><?=Yii::$app->request->hostInfo."/catalog/{$model->category->slug}/{$model->slug}/"?></span>
            </a>
            <p id="seo-snippet-desc" class="seo-snippet-desc"><?=$model->seo_desc ?: 'Seo Описание'?></p>
        </div>
    </div>
</div>

<br />
<br />

<div class="row">
    <div class="col-xs-6">
        <?= $form->field($model, 'seo_title')->textInput([
            'id' => 'seo-title-field',
        ]) ?>
        <?= $form->field($model, 'seo_keywords')->textInput([
            'id' => 'seo-keywords-field',
        ]) ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'seo_desc')->textarea([
            'style'=>'height: 108px',
            'id' => 'seo-desc-field',
        ]) ?>
    </div>
</div>
<br />
