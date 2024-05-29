<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\ProductCategoryConfig
 * @var $categoryConfig_s common\models\ProductCategoryConfig[]
 */

$this->title = t_b('Редактировать конфигурацию продуктов: ') . ' ' . $model->title;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = ['label' => t_b('Конфигурации продуктов'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

?>

<?php echo $this->render('_form', [
    'model' => $model
]) ?>
