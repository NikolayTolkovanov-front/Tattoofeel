<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Product
 * @var $prices      common\models\ProductPrice
 * @var $categories common\models\ProductCategory[]
 * @var $categoriesConfig
 * @var $pricesError
 */

$model->created_at = Yii::$app->formatter->asDateTime($model->created_at);
$model->updated_at = Yii::$app->formatter->asDateTime($model->updated_at);

$model->created_by = $model->author->username??'';
$model->updated_by = $model->updater->username??'';

$model->revised = 1;
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
]) ?>

<div class="container-fluid">

    <?= Tabs::widget([
        'encodeLabels' => false,
        'items' => [
            [
                'label' => 'Данные',
                'content' => $this->render('_form_data', [
                    'model' => $model,
                    'form' => $form,
                    'categories' => $categories,
                    'categoriesConfig' => $categoriesConfig
                ]),
            ],
            [
                'label' => 'Цены',
                'content' => $this->render('_form_price', [
                    'model' => $model,
                    'form' => $form,
                    'prices' => $prices
                ])
            ],
            [
                'label' => 'Описание',
                'content' => $this->render('_form_desc', ['model' => $model, 'form' => $form])
            ],
            [
                'label' => 'Фильтры',
                'content' => $this->render('_form_filter', ['model' => $model, 'form' => $form])
            ],
            [
                'label' => 'Медиа',
                'content' => $this->render('_form_media', ['model' => $model, 'form' => $form])
            ],
            [
                'label' => 'Вопрос-ответ',
                'content' => $this->render('_form_questions', ['model' => $model, 'form' => $form])
            ],
            [
                'label' => 'C этим тов. покупают',
                'content' => $this->render('_form_related', ['model' => $model, 'form' => $form])
            ],
            [
                'label' => 'Синхронизация',
                'content' => $this->render('_form_sync', ['model' => $model, 'form' => $form])
            ],
            [
                'label' => 'SEO',
                'content' => $this->render('_form_seo', ['model' => $model, 'form' => $form])
            ],
        ],
    ]) ?>

    <?php //валидация
        $this->registerJs("
            function check() {
                var validate = true;
                
                $('.nav-tabs').closest('form').find('.tab-pane').each(function(){
                    if ($(this).find('.has-error').length) {
                        $('[href=\"#' + $(this).attr('id') + '\"]').click();
                        validate = false;
                    }
                })                
                
                if (!validate)
                    return false;
            }
            $('.nav-tabs').closest('form').on('beforeValidate', function(){
                check();
            });
            check();
            
            $('.redactor-box').on('keyup', function(){
                if ($($(this).find('textarea').val()).text())
                    $(this).closest('.has-error').removeClass('has-error');
            })
        ");
    ?>

    <div class="form-group">
        <?php echo  !$model->isNewRecord ? Html::submitButton( Yii::t('backend', 'Обновить'),
            ['class' => 'btn btn-primary', 'name' => 'update', 'value' => 1,
                'disabled' => (bool) Yii::$app->keyStorage->get('backend.products.sync.isStart')]) : null ?>
        <?php echo Html::submitButton( Yii::t('backend', 'Сохранить'),
            ['class' => 'btn btn-success', 'name' => 'save', 'value' => 1,
                'disabled' => (bool) Yii::$app->keyStorage->get('backend.products.sync.isStart')]) ?>
        <?php echo Html::a( Yii::t('backend', 'Отменить'), Url::to(['index']),
            ['class' => 'btn btn-default']) ?>
    </div>

</div>

<?php ActiveForm::end() ?>
