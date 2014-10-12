<?php

namespace vova07\comments\controllers\backend;

use vova07\admin\components\Controller;
use vova07\comments\models\backend\Comment;
use vova07\comments\models\backend\CommentSearch;
use vova07\comments\models\backend\Model;
use Yii;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Default backend controller.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'] = [
            [
                'allow' => true,
                'actions' => ['index'],
                'roles' => ['BViewComments']
            ]
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['update'],
            'roles' => ['BUpdateComments']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['delete', 'batch-delete'],
            'roles' => ['BDeleteComments']
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'index' => ['get'],
                'update' => ['get', 'put', 'post'],
                'delete' => ['post', 'delete'],
                'batch-delete' => ['post', 'delete']
            ]
        ];

        return $behaviors;
    }

    /**
     * Comments list page.
     */
    public function actionIndex()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $statusArray = Comment::getStatusArray();
        $modelArray = Model::getModelArray();

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'statusArray' => $statusArray,
                'modelArray' => $modelArray
            ]);
    }

    /**
     * Update comment page.
     *
     * @param integer $id Comment ID
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('admin-update');
        $statusArray = Comment::getStatusArray();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->refresh();
                } else {
                    Yii::$app->session->setFlash('danger', Module::t('comments', 'BACKEND_FLASH_FAIL_ADMIN_UPDATE'));
                    return $this->refresh();
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('update', [
                'model' => $model,
                'statusArray' => $statusArray
            ]);
    }

    /**
     * Delete comment page.
     *
     * @param integer $id Comment ID
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteComment();
        return $this->redirect(['index']);
    }

    /**
     * Delete multiple comments page.
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionBatchDelete()
    {
        if (($ids = Yii::$app->request->post('ids')) !== null) {
            $models = $this->findModel($ids);
            foreach ($models as $model) {
                $model->deleteComment();
            }
            return $this->redirect(['index']);
        } else {
            throw new HttpException(400);
        }
    }

    /**
     * Find model by ID.
     *
     * @param integer|array $id Comment ID
     *
     * @return \vova07\comments\models\backend\Comment Model
     *
     * @throws HttpException 404 error if comment not found
     */
    protected function findModel($id)
    {
        if (is_array($id)) {
            /** @var \vova07\comments\models\backend\Comment $model */
            $model = Comment::findAll($id);
        } else {
            /** @var \vova07\comments\models\backend\Comment $model */
            $model = Comment::findOne($id);
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new HttpException(404);
        }
    }
}
