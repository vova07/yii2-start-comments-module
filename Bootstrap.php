<?php

namespace vova07\comments;

use yii\base\BootstrapInterface;

/**
 * Gallery module bootstrap class.
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        // Add module URL rules.
        $app->getUrlManager()->addRules(
            [
                'POST <_m:galleries>' => '<_m>/default/create',
                '<_m:galleries>' => '<_m>/default/index',
                '<_m:galleries>/<id:\d+>-<alias:[a-zA-Z0-9_-]{1,100}+>' => '<_m>/default/view',
            ]
        );

        // Add module I18N category.
        if (!isset($app->i18n->translations['vova07/comments']) && !isset($app->i18n->translations['vova07/*'])) {
            $app->i18n->translations['vova07/comments'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vova07/comments/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'vova07/comments' => 'comments.php',
                ]
            ];
        }
        if (!isset($app->i18n->translations['vova07/comments-models']) && !isset($app->i18n->translations['vova07/*'])) {
            $app->i18n->translations['vova07/comments-models'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vova07/comments/messages',
                'forceTranslation' => true,
                'fileMap' => [
                    'vova07/comments-models' => 'comments-models.php',
                ]
            ];
        }
    }
}
