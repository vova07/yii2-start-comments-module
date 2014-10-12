<?php

/**
 * Comment model update view.
 *
 * @var \yii\base\View $this View
 * @var \vova07\comments\models\backend\Model $model Model
 * @var \vova07\themes\admin\widgets\Box $box Box widget instance
 * @var array $statusArray Statuses array
 */

use vova07\comments\Module;
use vova07\themes\admin\widgets\Box;

$this->title = Module::t('comments-models', 'BACKEND_UPDATE_TITLE');
$this->params['subtitle'] = Module::t('comments-models', 'BACKEND_UPDATE_SUBTITLE');
$this->params['breadcrumbs'] = [
    [
        'label' => $this->title,
        'url' => ['index'],
    ],
    $this->params['subtitle']
];
$boxButtons = ['{cancel}'];

if (Yii::$app->user->can('BCreateCommentsModels')) {
    $boxButtons[] = '{create}';
}
if (Yii::$app->user->can('BDeleteCommentsModels')) {
    $boxButtons[] = '{delete}';
}
$boxButtons = !empty($boxButtons) ? implode(' ', $boxButtons) : null; ?>
<div class="row">
    <div class="col-sm-12">
        <?php $box = Box::begin(
            [
                'title' => $this->params['subtitle'],
                'renderBody' => false,
                'options' => [
                    'class' => 'box-success'
                ],
                'bodyOptions' => [
                    'class' => 'table-responsive'
                ],
                'buttonsTemplate' => $boxButtons
            ]
        );
        echo $this->render(
            '_form',
            [
                'model' => $model,
                'statusArray' => $statusArray,
                'box' => $box
            ]
        );
        Box::end(); ?>
    </div>
</div>
