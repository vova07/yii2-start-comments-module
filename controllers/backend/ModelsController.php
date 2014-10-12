<?php

namespace vova07\comments\controllers\backend;

use vova07\admin\components\Controller;
use vova07\comments\models\backend\Model;
use vova07\comments\models\backend\ModelSearch;
use vova07\comments\Module;
use Yii;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Comments models backend controller.
 */
class ModelsController extends Controller
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
                'roles' => ['BViewCommentsModels']
            ]
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['create'],
            'roles' => ['BCreateCommentsModels']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['update'],
            'roles' => ['BUpdateCommentsModels']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['delete', 'batch-delete'],
            'roles' => ['BDeleteCommentsModels']
        ];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => ['enable', 'disable'],
            'roles' => ['BManageCommentsModule']
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'index' => ['get'],
                'view' => ['get'],
                'create' => ['get', 'post'],
                'update' => ['get', 'put', 'post'],
                'delete' => ['post', 'delete'],
                'batch-delete' => ['post', 'delete'],
                'enable' => ['post'],
                'disable' => ['post']
            ]
        ];

        return $behaviors;
    }

    /**
     * Comment models list page.
     */
    public function actionIndex()
    {
        $searchModel = new ModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $statusArray = Model::getStatusArray();

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'statusArray' => $statusArray
            ]);
    }

    /**
     * Create model page.
     */
    public function actionCreate()
    {
        $model = new Model(['scenario' => 'admin-create']);
        $statusArray = Model::getStatusArray();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->redirect(['update', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('danger', Module::t('comments-models', 'BACKEND_FLASH_FAIL_ADMIN_CREATE'));
                    return $this->refresh();
                }
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('create', [
                'model' => $model,
                'statusArray' => $statusArray
            ]);
    }

    /**
     * Update model page.
     *
     * @param integer $id Post ID
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('admin-update');
        $statusArray = Model::getStatusArray();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    return $this->refresh();
                } else {
                    Yii::$app->session->setFlash('danger', Module::t('comments-models', 'BACKEND_FLASH_FAIL_ADMIN_UPDATE'));
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
     * Delete model page.
     *
     * @param integer $id Post ID
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Delete multiple models page.
     *
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionBatchDelete()
    {
        if (($ids = Yii::$app->request->post('ids')) !== null) {
            $models = $this->findModel($ids);
            foreach ($models as $model) {
                $model->delete();
            }
            return $this->redirect(['index']);
        } else {
            throw new HttpException(400);
        }
    }

    /**
     * Enable comments for indicated extension
     *
     * @param string $name Extension name
     *
     * @return mixed
     */
    public function actionEnable($name)
    {
        if (!Model::enableExtension($name)) {
            Yii::$app->session->setFlash('danger', Module::t('comments-models', 'BACKEND_FLASH_FAIL_ADMIN_ENABLE'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Disable comments for indicated extension
     *
     * @param string $name Extension name
     *
     * @return mixed
     */
    public function actionDisable($name)
    {
        if (!Model::disableExtension($name)) {
            Yii::$app->session->setFlash('danger', Module::t('comments-models', 'BACKEND_FLASH_FAIL_ADMIN_DISABLE'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Find model by ID.
     *
     * @param integer|array $id Model ID
     *
     * @return \vova07\comments\models\backend\Model Model
     *
     * @throws HttpException 404 error if model not found
     */
    protected function findModel($id)
    {
        if (is_array($id)) {
            /** @var \vova07\comments\models\backend\Model $model */
            $model = Model::findAll($id);
        } else {
            /** @var \vova07\comments\models\backend\Model $model */
            $model = Model::findOne($id);
        }
        if ($model !== null) {
            return $model;
        } else {
            throw new HttpException(404);
        }
    }
}
