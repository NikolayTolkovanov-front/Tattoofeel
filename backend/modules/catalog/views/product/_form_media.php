<?php

use backend\widgets\form\upload\Upload;
use backend\widgets\form\upload\UploadMany;
use trntv\aceeditor\AceEditor;

?>
<br />
<div class="row">
    <div class="col-xs-12">
        <?= $form->field($model, 'thumbnail')->widget(Upload::class) ?>
        <?= $form->field($model, 'attachments')->widget(UploadMany::class) ?>
        <?= $form->field($model, 'video_code')->widget(
            trntv\aceeditor\AceEditor::class,
            [
                'mode' => 'html',
            ]
        ) ?>
    </div>
</div>
