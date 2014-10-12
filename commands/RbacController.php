<?php

namespace vova07\comments\commands;

use Yii;
use yii\console\Controller;

/**
 * Comments RBAC controller.
 */
class RbacController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'add';

    /**
     * @var array Main module permission array
     */
    public $mainPermission = [
        'name' => 'administrateComments',
        'description' => 'Can administrate all "Comments" module'
    ];

    /**
     * @var array Permission
     */
    public $permissions = [
        'BViewCommentsModels' => [
            'description' => 'Can view backend comments models list',
        ],
        'BCreateCommentsModels' => [
            'description' => 'Can create backend comments models'
        ],
        'BUpdateCommentsModels' => [
            'description' => 'Can update backend comments models'
        ],
        'BDeleteCommentsModels' => [
            'description' => 'Can delete backend comments models'
        ],
        'BManageCommentsModule' => [
            'description' => 'Can enable or disable comments for installed modules'
        ],
        'BViewComments' => [
            'description' => 'Can view backend comments list'
        ],
        'BUpdateComments' => [
            'description' => 'Can update backend comments'
        ],
        'BDeleteComments' => [
            'description' => 'Can delete backend comments'
        ],
        'viewComments' => [
            'description' => 'Can view comments'
        ],
        'createComments' => [
            'description' => 'Can create comments'
        ],
        'updateComments' => [
            'description' => 'Can update comments'
        ],
        'updateOwnComments' => [
            'description' => 'Can update own comments',
            'rule' => 'author'
        ],
        'deleteComments' => [
            'description' => 'Can delete comments'
        ],
        'deleteOwnComments' => [
            'description' => 'Can delete own comments',
            'rule' => 'author'
        ]
    ];

    /**
     * Add comments RBAC.
     */
    public function actionAdd()
    {
        $auth = Yii::$app->authManager;
        $superadmin = $auth->getRole('superadmin');
        $mainPermission = $auth->createPermission($this->mainPermission['name']);
        if (isset($this->mainPermission['description'])) {
            $mainPermission->description = $this->mainPermission['description'];
        }
        if (isset($this->mainPermission['rule'])) {
            $mainPermission->ruleName = $this->mainPermission['rule'];
        }
        $auth->add($mainPermission);

        foreach ($this->permissions as $name => $option) {
            $permission = $auth->createPermission($name);
            if (isset($option['description'])) {
                $permission->description = $option['description'];
            }
            if (isset($option['rule'])) {
                $permission->ruleName = $option['rule'];
            }
            $auth->add($permission);
            $auth->addChild($mainPermission, $permission);
        }

        $auth->addChild($superadmin, $mainPermission);

        $updateComments = $auth->getPermission('updateComments');
        $updateOwnComments = $auth->getPermission('updateOwnComments');
        $deleteComments = $auth->getPermission('deleteComments');
        $deleteOwnComments = $auth->getPermission('deleteOwnComments');

        $auth->addChild($updateComments, $updateOwnComments);
        $auth->addChild($deleteComments, $deleteOwnComments);

        return static::EXIT_CODE_NORMAL;
    }

    /**
     * Remove comments RBAC.
     */
    public function actionRemove()
    {
        $auth = Yii::$app->authManager;
        $permissions = array_keys($this->permissions);

        foreach ($permissions as $name => $option) {
            $permission = $auth->getPermission($name);
            $auth->remove($permission);
        }

        $mainPermission = $auth->getPermission($this->mainPermission['name']);
        $auth->remove($mainPermission);

        return static::EXIT_CODE_NORMAL;
    }
}
