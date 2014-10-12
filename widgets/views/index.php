<?php

/**
 * Comments list view.
 *
 * @var \yii\web\View $this View
 * @var \vova07\comments\models\frontend\Comment[] $models Comments models
 * @var \vova07\comments\models\frontend\Comment $model New comment model
 */

use vova07\comments\Module;

?>

<div id="comments">
    <div id="comments-list" data-comment="list">
        <?= $this->render('_index_item', ['models' => $models]) ?>
    </div>
    <!--/ #comments-list -->

    <?php if (Yii::$app->user->can('createComments')) : ?>
        <h3><?= Module::t('comments', 'FRONTEND_WIDGET_COMMENTS_FORM_TITLE') ?></h3>
        <?= $this->render('_form', ['model' => $model]); ?>
    <?php endif; ?>
</div>