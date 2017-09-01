Module for SkeekS CMS
===================================

[![Latest Stable Version](https://poser.pugx.org/v3toys/skeeks/v/stable.png)](https://packagist.org/packages/v3toys/skeeks)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist v3toys/skeeks "*"
```

or add

```
"v3toys/skeeks": "*"
```

Configuration app
----------

```php

'components' =>
[
    'dbV3project' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'pgsql:host=db.v3project.ru;port=5432;dbname=v3toys_ru',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8',
    ],
        
    'v3toysApi' =>
    [
        'class' => 'v3toys\yii2\api\Api'
    ],

    'v3toysSettings' =>
    [
        'class' => 'v3toys\skeeks\components\V3toysSettings'
    ],

    'i18n' =>
    [
        'translations' =>
        [
            'v3toys/skeeks' =>
            [
                'class'             => 'yii\i18n\PhpMessageSource',
                'basePath'          => '@v3toys/skeeks/messages',
                'fileMap' => [
                    'v3toys/skeeks' => 'main.php',
                ],
            ]
        ]
    ],
],

'modules' =>
[
    'v3toys' =>
    [
        'class'                 => 'v3toys\skeeks\V3toysModule',
    ]
]

```

___

> [![skeeks!](https://gravatar.com/userimage/74431132/13d04d83218593564422770b616e5622.jpg)](http://skeeks.com)  
<i>SkeekS CMS (Yii2) — quickly, easily and effectively!</i>  
[skeeks.com](http://skeeks.com) | [en.cms.skeeks.com](http://en.cms.skeeks.com) | [cms.skeeks.com](http://cms.skeeks.com) | [marketplace.cms.skeeks.com](http://marketplace.cms.skeeks.com)


