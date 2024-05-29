<?php

use yii\helpers\ArrayHelper;
use common\models\Question;

?>
<br />
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'questions')->dropDownList(
            ArrayHelper::map(Question::find()->andWhere([Question::tableName().'.status' => 1])->all(), 'id', 'title'),
            ['multiple' => true,'style'=>'height: 500px']
        ) ?>
    </div>
</div>

