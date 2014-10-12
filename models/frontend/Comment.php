<?php

namespace vova07\comments\models\frontend;

use Yii;

/**
 * This is the model class for table "{{%comments}}".
 *
 * @property integer $id ID
 * @property integer $model_class Model class ID
 * @property integer $model_id Model ID
 * @property integer $author_id Author ID
 * @property string $content Content
 * @property integer $status_id Status
 * @property integer $created_at Create time
 * @property integer $updated_at Update time
 *
 * @property string $created Human readable created time
 * @property string $updated Human readable updated time
 *
 * @property \vova07\users\models\User $author Author
 * @property Model $model Model
 */
class Comment extends \vova07\comments\models\Comment
{
    /**
     * @var string Created date
     */
    private $_created;

    /**
     * @var string Updated date
     */
    private $_updated;

    /**
     * @return string Created date
     */
    public function getCreated()
    {
        if ($this->_created === null) {
            $this->_created = Yii::$app->formatter->asDate($this->created_at, 'd LLL Y');
        }

        return $this->_created;
    }

    /**
     * @return string Updated date
     */
    public function getUpdated()
    {
        if ($this->_updated === null) {
            $this->_updated = Yii::$app->formatter->asDate($this->updated_at, 'd LLL Y');
        }

        return $this->_updated;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['parent_id', 'model_class', 'model_id', 'content'],
            'update' => ['content'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                if (!$this->author_id) {
                    $this->author_id = Yii::$app->user->id;
                }
                if (!$this->status_id) {
                    $this->status_id = self::STATUS_ACTIVE;
                }
            }

            return true;
        } else {
            return false;
        }
    }
}
