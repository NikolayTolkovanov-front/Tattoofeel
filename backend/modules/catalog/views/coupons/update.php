<?php

/**
 * @var $this       yii\web\View
 * @var $model      common\models\Product
 * @var $brands      common\models\Brand[]
 * @var $categories common\models\ProductCategory[]
 */

$this->title = t_b('Редактировать купон:') . ' ' . $model->coupon_code;

$this->params['breadcrumbs'][] = ['label' => t_b('Каталог'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php echo $this->render('_form', [
    'model' => $model,
    'brands' => $brands,
    'categories' => $categories,
]); ?>
