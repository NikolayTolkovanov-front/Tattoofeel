<?php

/**
 * @var $this       yii\web\View
 * @var $import
 */

$this->title = t_b('Импорт/Экспорт фильтров');

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/product-filters']];
$this->params['breadcrumbs'][] = ['label' => t_b('Фильтры'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($import, 'xlsx')->fileInput([
            'accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel'
    ]) ?>

    <?= $form->field($import, 'deleted_all')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton( Yii::t('backend', 'Импортировать'),
            ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('backend', 'Экспортировать'), Url::to(['/catalog/product-filters/export']),
            ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end() ?>
