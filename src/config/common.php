<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 27.08.2015
 */
return [

    'components' => [

        'cmsExport' => [
            'handlers'     => [
                \v3toys\skeeks\components\V3ExportShopYandexMarketHandler::class => [
                    'class' => \v3toys\skeeks\components\V3ExportShopYandexMarketHandler::class
                ]
            ]
        ],

        'cmsAgent' => [
            'commands' => [

                'v3toys/init/order-statuses' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Импорт статусов из v3toys в базу сайта',
                    'interval' => 3600 * 24,
                ],

                'v3toys/agents/submit-new-orders' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Отправка новых заказов в v3toys',
                    'interval' => 180,
                ],

                'v3toys/agents/orders-update' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Обновление статусов заказов из v3toys',
                    'interval' => 180,
                ],

                'v3toys/agents/orders-update 300' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Обновление всех заказов из v3toys',
                    'interval' => 3600 * 24,
                ],

                'v3toys/agents/submit-new-messages' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Отправка новых заявок в v3toys',
                    'interval' => 180,
                ],

                'v3toys/agents/messages-update' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Обновление статусов заявок из v3toys',
                    'interval' => 180,
                ],

                'v3toys/properties/load' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Обновление свойств у товаров (только у которых нет)',
                    'interval' => 600,
                ],

                'v3toys/properties/load 1' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Обновление свойств по всем товарам',
                    'interval' => 604800,
                ],

                'v3toys/prices/load' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Обновление свойств по всем товарам',
                    'interval' => 10600,
                ],

                'v3toys/products/load' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Получение и обновление данных по товарам аффилиата',
                    'interval' => 3600,
                ],

                'v3toys/products/load affproduct_v2' => [
                    'class' => \skeeks\cms\agent\CmsAgent::class,
                    'name' => 'Получение и обновление данных по товарам аффилиата v2',
                    'interval' => 3600,
                ],
            ]
        ],

    ],
];