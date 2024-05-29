<?php
/**
 * @var $productsRecently
 * @var $search
 * @var $users
 */

use frontend\widgets\products\row\ProductsRow;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::$app->params['title'] . ' | Личный кабинет | Войти как...';

$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => Url::to(['/lk'])];
$this->params['breadcrumbs'][] = ['label' => 'Войти как...'];
?>

<section style="padding-top:20px;">
    <div class="container">
        <div class="grid-right-col">
            <div class="grid-right-col__main">
                <div class="lk__box">
                    <h1 class="h3">Войти как...</h1>

                    <?php $form = ActiveForm::begin([
                        'action' => '/lk/login-as/',
                        'method' => 'get',
                        'fieldConfig' => [
                            'options' => ['class' => 'lk-profile__input'],
                            'template' => "{label}\n{input}<span></span>\n{error}\n",
                            'inputOptions' => ['placeholder' => 'Введите значение', 'class' => 'placeholder']
                        ],
                    ]);?>

                    <div class="lk-profile">
                        <?php echo $form->field($search, 'full_name')->textInput()
                            ->label('ФИО:') ?>

                        <?php echo $form->field($search, 'phone')->textInput([
                            //'data-inputmask' => "'mask': '+7 (999) 999-99-99'"
                        ])->label('Телефон:') ?>


                        <?php echo $form->field($search, 'email')->textInput([
                            //'data-inputmask' => "'alias': 'email_cr'"
                        ])->label('E-mail:') ?>
                    </div>
                    <div class="lk-profile-btn">
                        <input class="btn _big" type="submit" value="Найти" />
                    </div>
                    <?php ActiveForm::end() ?>

                    <?php if(isset($users)):?>
                        <div class="lk-table-wrap">
                            <?php if(!count($users)) {?>
                                <div class="lk-table">
                                    <div class="lk-table__row">
                                        <div class="lk-table-fd-number">Пользователей не найдено</div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="lk-table">
                                    <div class="lk-table__head">
                                        <div class="lk-table-fd-number">ID</div>
                                        <div class="lk-table-fd-date">ФИО</div>
                                        <div class="lk-table-fd-sum11">Телефон</div>
                                        <div class="lk-table-fd-status">E-mail</div>
                                        <div class="lk-table-fd-detail"></div>
                                    </div>
                                    <?php foreach($users as $user) {?>
                                        <div class="lk-table__row">
                                            <div class="lk-table-fd-number"><span><?=$user->id?></span></div>
                                            <div class="lk-table-fd-date"><?=$user->userProfile->full_name?></div>
                                            <div class="lk-table-fd-sum1"><?=$user->userProfile->phone?></div>
                                            <div class="lk-table-fd-status"><?=$user->email?></div>
                                            <div class="lk-table-fd-detail">
                                                <?php $form = ActiveForm::begin([
                                                    'id' => 'login-as-form',
                                                    'options' => ['method' => 'post'],
                                                    'action' => '/lk/login-by-id/',
                                                ]) ?>

                                                <input type="hidden" name="user_id" value="<?=$user->id?>" />
                                                <button type="submit" class="btn">Войти как</button>

                                                <?php ActiveForm::end() ?>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php endif;?>
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

