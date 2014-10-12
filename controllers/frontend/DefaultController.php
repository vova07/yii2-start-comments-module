<?php

namespace vova07\comments\controllers\frontend;

use vova07\comments\models\frontend\Comment;
use vova07\comments\Module;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Default controller.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['post'],
                'update' => ['put', 'post'],
                'delete' => ['post', 'delete']
            ]
        ];

        return $behaviors;
    }

    /**
     * Create comment.
     */
    public function actionCreate()
    {
        $model = new Comment(['scenario' => 'create']);
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->tree($model);
                } else {
                    Yii::$app->response->setStatusCode(500);
                    return Module::t('comments', 'FRONTEND_FLASH_FAIL_CREATE');
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->setStatusCode(400);
                return ActiveForm::validate($model);
            }
        }
    }

    /**
     * Update comment.
     *
     * @param integer $id Comment ID
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update');
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $model->content;
                } else {
                    Yii::$app->response->setStatusCode(500);
                    return Module::t('comments', 'FRONTEND_FLASH_FAIL_UPDATE');
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->setStatusCode(400);
                return ActiveForm::validate($model);
            }
        }
    }

    /**
     * Delete comment page.
     *
     * @param integer $id Comment ID
     *
     * @return string Comment content
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($this->findModel($id)->deleteComment()) {
            return Module::t('comments', 'FRONTEND_WIDGET_COMMENTS_DELETED_COMMENT_TEXT');
        } else {
            Yii::$app->response->setStatusCode(500);
            return Module::t('comments', 'FRONTEND_FLASH_FAIL_DELETE');
        }
    }

    /**
     * Find model by ID.
     *
     * @param integer|array $id Comment ID
     *
     * @return Comment Model
     *
     * @throws HttpException 404 error if comment not found
     */
    protected function findModel($id)
    {
        /** @var Comment $model */
        $model = Comment::findOne($id);

        if ($model !== null) {
            return $model;
        } else {
            throw new HttpException(404, Module::t('comments', 'FRONTEND_FLASH_RECORD_NOT_FOUND'));
        }
    }

    /**
     * @param Comment $model Comment
     *
     * @return string Comments list
     */
    protected function tree($model)
    {
        $models = Comment::getTree($model->model_id, $model->model_class);
        return $this->renderPartial('@vova07/comments/widgets/views/_index_item', ['models' => $models]);
    }
}
