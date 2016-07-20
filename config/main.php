<?php
return [

    'components' =>
    [
        'v3toysApi' => 
        [
            //'class' => 'v3toys\yii2\api\Api'
            'class' => 'v3toys\skeeks\V3toysApi'
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
];