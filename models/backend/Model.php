<?php

namespace vova07\comments\models\backend;

use Yii;
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
class Model extends \vova07\comments\models\Model
{
    /**
     * @var array|null Installed extension array
     */
    protected static $_installedExtensions;

    /**
     * @var array Allowed extensions name
     */
    public static $extensions = [
        'blogs' => 'vova07\blogs\models\frontend\Blog'
    ];

    /**
     * @param string $name Extension name
     *
     * @return boolean Whether extension is enabled or not
     */
    public static function isExtensionEnabled($name)
    {
        if (isset(self::$extensions[$name])) {
            if (self::$_installedExtensions === null) {
                self::$_installedExtensions = ArrayHelper::getColumn(self::find()->select('name')->asArray()->all(), 'name');
            }

            return in_array(self::$extensions[$name], self::$_installedExtensions);
        } else {
            return false;
        }
    }

    /**
     * Enable comments for indicated extension
     *
     * @param string $name Extension name
     *
     * @return boolean Whether extension is saved or not
     */
    public static function enableExtension($name)
    {
        if (isset(self::$extensions[$name])) {
            $model = new Model();
            $model->name = self::$extensions[$name];

            return $model->save();
        }

        return false;
    }

    /**
     * Disable comments for indicated extension
     *
     * @param string $name Extension name
     *
     * @return boolean Whether extension is saved or not
     */
    public static function disableExtension($name)
    {
        if (isset(self::$extensions[$name])) {
            if (($model = self::findIdentity(self::$extensions[$name])) !== null) {
                return $model->delete();
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['admin-create'] = ['name'];
        $scenarios['admin-update'] = ['name'];

        return $scenarios;
    }
}
