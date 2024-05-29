<?php
/**
 * @var $reviews
 * @var $product_id
 * @var $product_title
 * @var $is_main_config
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\HtmlPurifier;

//function BITGetDeclNum($value = 1, $status = array('', 'а', 'ов')) {
//    $array = array(2, 0, 1, 1, 1, 2);
//    return $status[($value % 100 > 4 && $value % 100 < 20) ? 2 : $array[($value % 10 < 5) ? $value % 10 : 5]];
//}

function GetMonthTitle($value) {
    $value = strtr($value, array(
        "01" => "Января",
        "02" => "Февраля",
        "03" => "Марта",
        "04" => "Апреля",
        "05" => "Мая",
        "06" => "Июня",
        "07" => "Июля",
        "08" => "Августа",
        "09" => "Сентября",
        "10" => "Октября",
        "11" => "Ноября",
        "12" => "Декабря"));

    return $value;
}
?>

<div class="review">
    <?php if (!$is_main_config):?>
        <noindex>
    <?php endif;?>

    <?php if (!empty($reviews)):?>
        <h1 class="h3"><?=count($reviews)?> отзыв<?=BITGetDeclNum(count($reviews));?> на <?=$product_title?></h1>
        <table class="review__wrapper">
            <?php foreach($reviews as $model) { ?>
                <tr>
                    <td>
                        <div class="review__avatar">
                            <span><?=mb_substr($model->userClient->profile->full_name, 0, 1)?></span>
                        </div>
                    </td>

                    <td>
                        <div class="review__rating">
                            <?php for ($i = 1; $i <= 5; $i++):?>
                                <?php if ($i <= $model->rating):?>
                                    <div class="review__full-star">
                                        <img src="/img/full_star.png">
                                    </div>
                                <?php else:?>
                                    <div class="review__empty-star">
                                        <img src="/img/empty_star.png">
                                    </div>
                                <?php endif;?>
                            <?php endfor;?>
                        </div>

                        <div class="review__owner-wrapper">
                            <div class="review__name"><?=$model->userClient->profile->full_name?></div>
                            <div class="review__date">
                                <?=Yii::$app->formatter->asDate($model->date, 'php:d')?>
                                <?=GetMonthTitle(Yii::$app->formatter->asDate($model->date, 'php:m'))?>
                                <?=Yii::$app->formatter->asDate($model->date, 'php:Y, H:i')?>
                            </div>
                        </div>

                        <div class="review__text">
                            <?= HtmlPurifier::process($model->text) ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php endif;?>

    <?php if (!$is_main_config):?>
        </noindex>
    <?php endif;?>

    <div class="review__form">
        <p class="h3">Добавить отзыв</p>
        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
        ]);?>
        <input type="hidden" name="ReviewForm[product_id]" value="<?=$product_id?>">

        <div class="countStarsBlock rowFields">
            <div class="textCountStars">Отзыв:</div>
            <div class="listElemStar">
                <div class="iconStar iconStar_1" data-check-val="1">
                    <div class="fullStars">
                        <img src="/img/full_star.png">
                    </div>
                    <div class="emptyStars">
                        <img src="/img/empty_star.png">
                    </div>
                </div>
                <div class="iconStar iconStar_2" data-check-val="2">
                    <div class="fullStars">
                        <img src="/img/full_star.png">
                    </div>
                    <div class="emptyStars">
                        <img src="/img/empty_star.png">
                    </div>
                </div>
                <div class="iconStar iconStar_3" data-check-val="3">
                    <div class="fullStars">
                        <img src="/img/full_star.png">
                    </div>
                    <div class="emptyStars">
                        <img src="/img/empty_star.png">
                    </div>
                </div>
                <div class="iconStar iconStar_4" data-check-val="4">
                    <div class="fullStars">
                        <img src="/img/full_star.png">
                    </div>
                    <div class="emptyStars">
                        <img src="/img/empty_star.png">
                    </div>
                </div>
                <div class="iconStar iconStar_5" data-check-val="5">
                    <div class="fullStars">
                        <img src="/img/full_star.png">
                    </div>
                    <div class="emptyStars">
                        <img src="/img/empty_star.png">
                    </div>
                </div>

                <input style="display:none" type="checkbox" name="ReviewForm[rating]" value="0" checked="">
            </div>
        </div>

        <div class="review__textarea <?php if (!!Yii::$app->user->isGuest) echo 'js-show-popup'?>">
            <textarea name="ReviewForm[text]" placeholder="Текст отзыва"></textarea>
        </div>

        <div class="review__send-btn">
            <input class="btn _big <?php if (!!Yii::$app->user->isGuest) echo 'js-show-popup'?>" type="submit" value="Отправить" />
        </div>
        <?php ActiveForm::end();?>
    </div>
</div>
