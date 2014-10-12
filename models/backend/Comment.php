<?php

namespace vova07\comments\models\backend;

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
 * @property \vova07\users\models\User $author Author
 * @property Model $model Model
 */
class Comment extends \vova07\comments\models\Comment
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['admin-update'] = ['status_id', 'content'];

        return $scenarios;
    }
}
