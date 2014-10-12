<?php

/**
 * Comments widget form view.
 * @var \yii\web\View $this View
 * @var \yii\widgets\ActiveForm $form Form
 * @var \vova07\comments\models\frontend\Comment $model New comment model
 */

use vova07\comments\Module;
use yii\helpers\Html;

?>
<?= Html::beginForm(['/comments/default/create'], 'POST', ['class' => 'form-horizontal', 'data-comment' => 'form', 'data-comment-action' => 'create']) ?>
    <div class="form-group" data-comment="form-group">
        <div class="col-sm-12">
            <?= Html::activeTextarea($model, 'content', ['class' => 'form-control']) ?>
            <?= Html::error($model, 'content', ['data-comment' => 'form-summary', 'class' => 'help-block hidden']) ?>
        </div>
    </div>
<?= Html::activeHiddenInput($model, 'parent_id', ['data-comment' => 'parent-id']) ?>
<?= Html::activeHiddenInput($model, 'model_class') ?>
<?= Html::activeHiddenInput($model, 'model_id') ?>
<?= Html::submitButton(Module::t('comments', 'FRONTEND_WIDGET_COMMENTS_FORM_SUBMIT'), ['class' => 'btn btn-danger btn-lg']); ?>
<?= Html::endForm(); ?>