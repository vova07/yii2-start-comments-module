Yii2-Start comments module.
==========================
This module provide a comments managing system for Yii2-Start application.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist vova07/yii2-start-comments-module "*"
```

or add

```
"vova07/yii2-start-comments-module": "*"
```

to the require section of your `composer.json` file.

Configuration
=============

- Add module to config section:

```
'modules' => [
    'comments' => [
        'class' => 'vova07\comments\Module'
    ]
]
```

- Run migrations:

```
php yii migrate --migrationPath=@vova07/comments/migrations
```

- Run RBAC command:

```
php yii comments/rbac/add
```

Usage:
------

- Add a new comment-on-able model `namespace` in `yii2-start.domain/backend/comments/models/index/`
- Add `Comments` widget in your view file:

```
echo \vova07\comments\widgets\Comments::widget(  
    [  
        'model' => $model,  
        'jsOptions' => [  
            'listSelector' => '[data-comment="list"]', // Comment list selector 
            'parentSelector' => '[data-comment="parent"]', // Comment parent selector
            'appendSelector' => '[data-comment="append"]', // Container selector where "reply" and "edit" form will be appended by jQuery
            'formSelector' => '[data-comment="form"]', // Comment form selector
            'contentSelector' => '[data-comment="content"]', // Comment content selector
            'toolsSelector' => '[data-comment="tools"]', // Comment tools selector
            'formGroupSelector' => '[data-comment="form-group"]', // Comment form group selector
            'errorSummarySelector' => '[data-comment="form-summary"]', // Comment form summary error selector
            'errorSummaryToggleClass' => 'hidden', // Comment summary error class that will be add/remove by jQuery on error reporting
            'errorClass' => 'has-error', // Comment form group error class
            'offset' => 0 // Top offset for scrollTo function. Use it if you have fixed top menu for correct scrolling to comment's parent. In case with fixed menu, "offset" value must be equal with menu block height.
        ]  
    ]  
);
```

- Profit!