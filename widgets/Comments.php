<?php

namespace vova07\comments\widgets;

use vova07\comments\Asset;
use vova07\comments\models\frontend\Comment;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Json;

class Comments extends Widget
{
    /**
     * @var \yii\db\ActiveRecord|null Widget model
     */
    public $model;

    /**
     * @var array Comments Javascript plugin options
     */
    public $jsOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->model === null) {
            throw new InvalidConfigException('The "model" property must be set.');
        }

        $this->registerClientScript();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $class = $this->model;
        $class = Yii::$app->base->crc32($class::className());
        $models = Comment::getTree($this->model->id, $class);
        $model = new Comment(['scenario' => 'create']);
        $model->model_class = $class;
        $model->model_id = $this->model->id;

        return $this->render('index', [
                'models' => $models,
                'model' => $model
            ]);
    }

    /**
     * Register widget client scripts.
     */
    protected function registerClientScript()
    {
        $view = $this->getView();
        $options = Json::encode($this->jsOptions);
        Asset::register($view);
        $view->registerJs('jQuery.comments(' . $options . ');');
    }
}
