<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Coupons
 * @var $brands     common\models\Brand[]
 * @var $categories common\models\ProductCategory[]
 */

$model->created_at = Yii::$app->formatter->asDateTime($model->created_at);
$model->updated_at = Yii::$app->formatter->asDateTime($model->updated_at);

$model->created_by = $model->author->username??'';
$model->updated_by = $model->updater->username??'';
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
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
                ]),
            ],
            [
                'label' => 'Действует на категории',
                'content' => $this->render('_form_categories', [
                    'model' => $model,
                    'form' => $form,
                    'categories' => $categories,
                ])
            ],
            [
                'label' => 'Действует на группы клиентов',
                'content' => $this->render('_form_client_groups', [
                    'model' => $model,
                    'form' => $form,
                    'groups' => array(
                        array(
                            'id' => 1,
                            'name' => 'Розничная цена'
                        ),
                        array(
                            'id' => 2,
                            'name' => 'Скидка 1'
                        ),
                        array(
                            'id' => 3,
                            'name' => 'Скидка 2'
                        ),
                        array(
                            'id' => 4,
                            'name' => 'Скидка 3'
                        ),
                        array(
                            'id' => 5,
                            'name' => 'Скидка 4'
                        ),
                        array(
                            'id' => 6,
                            'name' => 'Скидка 5'
                        ),
                        array(
                            'id' => 7,
                            'name' => 'Скидка 6'
                        ),
                    ),
                ])
            ],
            [
                'label' => 'Действует на бренды',
                'content' => $this->render('_form_brands', [
                    'model' => $model,
                    'form' => $form,
                    'brands' => $brands,
                ])
            ],
            [
                'label' => 'Действует на товары',
                'content' => $this->render('_form_products', [
                    'model' => $model,
                    'form' => $form,
                ])
            ],
            [
                'label' => 'Подарочные товары',
                'content' => $this->render('_form_products_gifts', [
                    'model' => $model,
                    'form' => $form,
                ])
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
        <?php echo !$model->isNewRecord ? Html::submitButton( Yii::t('backend', 'Обновить'),
            ['class' => 'btn btn-primary', 'name' => 'update', 'value' => 1]) : null ?>
        <?php echo Html::submitButton( Yii::t('backend', 'Сохранить'),
            ['class' => 'btn btn-success', 'name' => 'save', 'value' => 1]) ?>
        <?php echo Html::a( Yii::t('backend', 'Отменить'), Url::to(['index']),
            ['class' => 'btn btn-default']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
