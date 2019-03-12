<?php
return [

    'bootstrap' => ['v3toys'],

    'components' => [


        'v3toys' => [
            'class' => 'v3toys\skeeks\components\V3toysComponent',
        ],

        'i18n' => [
            'translations' => [
                'v3toys/skeeks' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@v3toys/skeeks/messages',
                    'fileMap'  => [
                        'v3toys/skeeks' => 'main.php',
                    ],
                ],
            ],
        ],

        'urlManager' => [
            'rules' => [
                '~child-<_a:(checkout|finish)>' => 'v3toys/cart/<_a>',
                '~child-order/<_a>'             => 'v3toys/order/<_a>',
                '~v3p-api/v04'                  => 'v3toys/api-v04/request',
                '~v3t/<_c>/<_a>'                => 'v3toys/<_c>/<_a>',
            ],
        ],
    ],

    'modules' => [
        'v3toys' => [
            'class' => 'v3toys\skeeks\V3toysModule',
        ],

        'shop' => [
            'controllerMap' => [
                'admin-cms-content-element' => 'v3toys\skeeks\controllers\AdminV3ShopCmsContentElementController',
            ],
        ],

        'seo' => [
            'controllerMap' => [
                'sitemap' => 'v3toys\skeeks\controllers\V3ProjectSitemapController',
            ],
        ],

        'cms' => [
            'controllerMap' => [
                'cms' => 'v3toys\skeeks\controllers\V3ProjectCmsController',
            ],
        ],
    ],
];