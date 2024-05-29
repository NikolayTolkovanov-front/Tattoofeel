<?php
/**
* @var $productsRecently
* @var $profile
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use frontend\widgets\products\row\ProductsRow;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Профиль';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Профиль'];
Yii::$app->user->identity->userProfile->sync();
?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Профиль</h1>
                    <?php $form = ActiveForm::begin([
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => true,
                    ]) ?>

                    <?php if ($profile->hasErrors()):?>
                        <div class="lk-register-smg">
                            <?php foreach($profile->getErrors() as $ee)
                                foreach($ee as $e)
                                    echo "<p class='help-block-error'>{$e}</p>" ?>
                        </div>
                    <?php endif;?>

                    <div class="lk-profile">
                        <div class="lk-profile-col">
                            <label class="like-ozn-labl">
                                <input name="UserClientProfile[full_name]" type="text" class="ozn-inp" value="<?=$profile->full_name?>" placeholder=" " readonly />
                                <span class="label-text">Фамилия Имя Отчество</span>
                            </label>

                            <label class="like-ozn-labl">
                                <input name="UserClientProfile[phone]" type="text" class="ozn-inp" value="<?=$profile->phone?>" placeholder=" " readonly />
                                <span class="label-text">Телефон</span>
                            </label>

                            <label class="like-ozn-labl">
                                <input name="UserClientProfile[phone_1]" type="text" class="ozn-inp" value="<?=$profile->phone_1?>" placeholder=" " />
                                <span class="label-text">Доп. телефон (необязательно)</span>
                            </label>

                            <label class="like-ozn-labl">
                                <input name="UserClientProfile[mail]" type="text" class="ozn-inp" value="<?=$profile->mail?>" placeholder=" " readonly />
                                <span class="label-text">Почта</span>
                            </label>

                            <label class="like-ozn-labl">
                                <textarea name="UserClientProfile[address_delivery]" class="ozn-inp" placeholder=" "><?=$profile->address_delivery?></textarea>
                                <span class="label-text">Город, улица, дом, квартира/офис</span>
                            </label>
                        </div>

                        <div class="lk-profile-col">
                            <label class="like-ozn-labl">
                                <input name="UserClientProfile[link_vk]" type="text" class="ozn-inp" value="<?=$profile->link_vk?>" placeholder=" " />
                                <span class="label-text">Вконтакте</span>
                            </label>

                            <label class="like-ozn-labl">
                                <input name="UserClientProfile[link_inst]" type="text" class="ozn-inp" value="<?=$profile->link_inst?>" placeholder=" " />
                                <span class="label-text">Instagram</span>
                            </label>

                            <div class="lk-profile-info">
                                <div class="info-title">Ваш менеджер</div>
                                <div class="info-text"><?=$profile->ms_owner_name_at_site ? $profile->ms_owner_name_at_site : ($profile->ms_owner ? $profile->ms_owner : 'Не назначен')?></div>
                            </div>

                            <div class="lk-profile-info">
                                <div class="info-title">Вконтакте</div>
                                <div class="info-text"><?=$profile->ms_owner_vk ? '<a href="'.$profile->ms_owner_vk.'" target="_blank">VK</a>' : 'Не известно'?></div>
                            </div>

                            <div class="lk-profile-info">
                                <div class="info-title">WhatsApp</div>
                                <div class="info-text"><?=$profile->ms_owner_whatsapp ? $profile->ms_owner_whatsapp : 'Не известно'?></div>
                            </div>

                            <div class="lk-profile-info">
                                <div class="info-title">Заказов</div>
                                <div class="info-text">
                                    <?=isset($profile->user->orders) && is_array($profile->user->orders) ? count($profile->user->orders) : 0?>
                                    на
                                    <?=number_format($profile->user->ordersSum / 100, 0, '.', ' ')?> <span class="rub">i</span>
                                </div>
                            </div>

                            <div class="lk-profile-info">
                                <div class="info-title">Скидка</div>
                                <div class="info-text"><?=$profile->sale_ms_id ?: 'Не назначена'?></div>
                            </div>

                            <div class="lk-profile-btn">
                                <input class="btn _big" type="submit" value="Сохранить" />
                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
            <aside class="grid-right-col__right">
                <?= $this->render('_menu') ?>
            </aside>
        </div>
    </div>
</section>

<?php if($productsRecently->getTotalCount()) { ?>
    <section>
        <div class="box-white">
            <div class="container container-slider-row">
                <?= ProductsRow::widget([
                    'title' => 'Популярные товары',
                    'dataProvider' => $productsRecently
                ])?>
            </div>
        </div>
    </section>
<?php } ?>
