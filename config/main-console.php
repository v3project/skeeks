<?php
return [

    'components' =>
    [
        'v3toysApi' =>
        [
            'class' => 'v3toys\yii2\api\Api'
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
            "controllerNamespace"   => 'v3toys\skeeks\console\controllers'
        ]
    ]
];