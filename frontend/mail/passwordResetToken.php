<?php

use yii\helpers\Html;

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/lk/reset', 'token' => $token]);
?>

Здравствуйте, <?php echo Html::encode($user->username) ?>,

Чтобы сбросить пароль, перейдите по ссылке ниже:

<?php echo Html::a(Html::encode($resetLink), $resetLink) ?>
