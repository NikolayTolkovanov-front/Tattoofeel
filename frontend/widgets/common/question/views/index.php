<?php
/**
* @var $questions
 */
use yii\helpers\HtmlPurifier;

?>

<?php if ( !empty($questions) ) { ?>
    <div class="question">
        <?php foreach($questions as $model) { ?>
            <div class="question__title">
                <?= $model->title ?>
            </div>
            <div class="question__answer block-typo">
                <?= HtmlPurifier::process($model->answer) ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
