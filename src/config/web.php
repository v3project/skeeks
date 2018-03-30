<?php
return [

    'bootstrap' => ['v3toys'],

    'components' =>
        [
            'dbV3project' => [
                'class' => 'v3toys\skeeks\V3DbConnection',
            ],

            'v3toysApi' => [
                'class' => 'v3toys\skeeks\V3toysApi'
            ],

            'v3projectApi' => [
                'class' => 'v3toys\skeeks\V3projectApi'
            ],

            'v3toysSettings' => [
                'class' => 'v3toys\skeeks\components\V3toysSettings'
            ],

            'v3toys' => [
                'class' => 'v3toys\skeeks\components\V3toysComponent'
            ],

            'i18n' => [
                'translations' =>
                    [
                        'v3toys/skeeks' =>
                            [
                                'class' => 'yii\i18n\PhpMessageSource',
                                'basePath' => '@v3toys/skeeks/messages',
                                'fileMap' => [
                                    'v3toys/skeeks' => 'main.php',
                                ],
                            ]
                    ]
            ],

            'urlManager' => [
                'rules' => [
                    '~child-<_a:(checkout|finish)>' => 'v3toys/cart/<_a>',
                    '~child-order/<_a>' => 'v3toys/order/<_a>',
                    '~v3p-api/v04' => 'v3toys/api-v04/request',
                    '~v3t/<_c>/<_a>' => 'v3toys/<_c>/<_a>',
                ]
            ],

            'canurl' => [
                'class' => 'v3project\helpers\CanUrl',
            ],

            //Не добавлять cannonical модулем seo, его добавит canUrl
            'seo' => [
                'canonicalPageParams' => false,
            ]
        ],

    'modules' => [
        'v3toys' => [
            'class' => 'v3toys\skeeks\V3toysModule',
        ],

        'shop' => [
            'controllerMap' => [
                'admin-cms-content-element' => 'v3toys\skeeks\controllers\AdminV3ShopCmsContentElementController',
            ]
        ],

        'seo' => [
            'controllerMap' => [
                'sitemap' => 'v3toys\skeeks\controllers\V3ProjectSitemapController',
            ]
        ],

        'cms' => [
            'controllerMap' => [
                'cms' => 'v3toys\skeeks\controllers\V3ProjectCmsController',
            ]
        ],
    ]
];