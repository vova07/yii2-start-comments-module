<?php

namespace vova07\comments\models;

use vova07\comments\Module;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%comments_models}}".
 *
 * @property integer $id ID
 * @property string $name Model class name
 * @property integer $status_id Status
 * @property integer $created_at Created time
 * @property integer $updated_at Updated time
 *
 * @property Comment[] $comments Comments
 */
class Model extends ActiveRecord
{
    /** Status disabled */
    const STATUS_DISABLED = 0;
    /** Status enabled */
    const STATUS_ENABLED = 1;

    /** Model array cache key */
    const CACHE_MODEL_ARRAY = 'cache-model-array';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comments_models}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className()
            ]
        ];
    }

    /**
     * Find model by ID.
     *
     * @param string|integer $id Model ID
     *
     * @return static|null Found model
     */
    public static function findIdentity($id)
    {
        $id = is_numeric($id) ? $id : Yii::$app->base->crc32($id);

        return self::findOne($id);
    }

    /**
     * @return array Status array
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_DISABLED => Module::t('comments-models', 'STATUS_DISABLED'),
            self::STATUS_ENABLED => Module::t('comments-models', 'STATUS_ENABLED')
        ];
    }

    /**
     * @return string Model readable status
     */
    public function getStatus()
    {
        return self::getStatusArray()[$this->status_id];
    }

    /**
     * @return array Model array
     */
    public static function getModelArray()
    {
        if (($array = Yii::$app->cache->get(self::CACHE_MODEL_ARRAY)) === false) {
            $array = ArrayHelper::map(self::find()->asArray()->all(), 'id', 'name');
            Yii::$app->cache->set(self::CACHE_MODEL_ARRAY, $array);
        }

        return $array;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Required
            ['name', 'required'],
            // String
            ['name', 'string', 'max' => 255],
            // Name
            ['name', 'unique'],
            // Status
            ['status_id', 'in', 'range' => array_keys(self::getStatusArray())],
            ['status_id', 'default', 'value' => self::STATUS_ENABLED]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('comments-models', 'ATTR_ID'),
            'name' => Module::t('comments-models', 'ATTR_NAME'),
            'status_id' => Module::t('comments-models', 'ATTR_STATUS'),
            'created_at' => Module::t('comments-models', 'ATTR_CREATED'),
            'updated_at' => Module::t('comments-models', 'ATTR_UPDATED'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->id = Yii::$app->base->crc32($this->name);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        Yii::$app->cache->delete(self::CACHE_MODEL_ARRAY);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            Comment::deleteAll(['model_class' => $this->id]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['model_class' => 'id']);
    }
}
