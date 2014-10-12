<?php

/**
 * Comment model form view.
 *
 * @var \yii\base\View $this View
 * @var \yii\widgets\ActiveForm $form Form
 * @var \vova07\comments\models\backend\Model $model Model
 * @var \vova07\themes\admin\widgets\Box $box Box widget instance
 * @var array $statusArray Statuses array
 */

use vova07\comments\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin(); ?>
<?php $box->beginBody(); ?>
    <div class="row">
        <div class="col-sm-12">
            <?=
            $form->field($model, 'name')->textInput(['placeholder' => Module::t('comments-models', 'BACKEND_CREATE_PLACEHOLDER_NAME')]) ?>
        </div>
    </div>
<?php $box->endBody(); ?>
<?php $box->beginFooter(); ?>
<?= Html::submitButton(
    $model->isNewRecord ? Module::t('comments-models', 'BACKEND_CREATE_SUBMIT') : Module::t('comments-models', 'BACKEND_UPDATE_SUBMIT'),
    [
        'class' => $model->isNewRecord ? 'btn btn-primary btn-large' : 'btn btn-success btn-large'
    ]
) ?>
<?php $box->endFooter(); ?>
<?php ActiveForm::end(); ?>