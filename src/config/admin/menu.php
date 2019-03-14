<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 15.04.2016
 */
return [
    'v3toys' => [
        'priority' => 400,
        'label'    => \Yii::t('v3toys/skeeks', 'v3toys'),
        "img"      => ['v3toys\skeeks\assets\V3toysAsset', 'icons/logo.png'],

        'items' => [

            [
                "label" => "Заказы",
                "url"   => ["v3toys/admin-order"],
            ],

            [
                "label" => "Заказы в 1 клик",
                "url"   => ["v3toys/admin-message"],
            ],

            [
                "label" => "Настройки",
                "img"   => ['\skeeks\cms\assets\CmsAsset', 'images/icons/settings-big.png'],

                'items' =>
                    [
                        [
                            "label" => "Статусы заказов",
                            "url"   => ["v3toys/admin-order-status"],
                        ],

                        /*[
                            "label"     => "Города доставки",
                            "url"       => ["v3toys/admin-shipping-city"],
                        ],*/

                        [
                            "label"          => "Настройки проекта",
                            "url"            => [
                                "cms/admin-settings",
                                "component" => 'v3toys\skeeks\components\V3toysSettings',
                            ],
                            "img"            => ['\skeeks\cms\assets\CmsAsset', 'images/icons/settings-big.png'],
                            "activeCallback" => function ($adminMenuItem) {
                                return (bool)(\Yii::$app->request->getUrl() == $adminMenuItem->getUrl());
                            },
                        ],


                        [
                            "label" => \Yii::t('v3toys/skeeks', "Api"),

                            'items' => [
                                [
                                    "label" => \Yii::t('v3toys/skeeks', "Общая информация"),
                                    "url"   => ["v3toys/admin-api-info"],
                                ],

                                [
                                    "label" => \Yii::t('v3toys/skeeks', "Статусы заказов"),
                                    "url"   => ["v3toys/admin-api-order-status"],
                                ],

                                [
                                    "label" => \Yii::t('v3toys/skeeks', "Заказы по телефону"),
                                    "url"   => ["v3toys/admin-api-orders"],
                                ],
                            ],
                        ],


                    ],
            ],
        ],
    ],
];