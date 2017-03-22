<?php
return
[
    'v3toys/init/order-statuses' =>
    [
        'description'       => 'Импорт статусов из v3toys в базу сайта',
        'agent_interval'    => 3600*24, //1 раз в сутки
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],

    'v3toys/agents/products-update' =>
    [
        'description'       => 'Обновление цен и наличия товаров',
        'agent_interval'    => 3600*0.5, //раз в 30 минут
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 3600*0.5,
        'is_period'         => 'N'
    ],

    'v3toys/agents/submit-new-orders' =>
    [
        'description'       => 'Отправка новых заказов в v3toys',
        'agent_interval'    => 180,
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],

    'v3toys/agents/orders-update' =>
    [
        'description'       => 'Обновление статусов заказов из v3toys',
        'agent_interval'    => 180,
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],

    'v3toys/agents/orders-update 300' =>
    [
        'description'       => 'Обновление всех заказов из v3toys',
        'agent_interval'    => 3600*24, //1 раз в сутки
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],

    'v3toys/agents/submit-new-messages' =>
    [
        'description'       => 'Отправка новых заявок в v3toys',
        'agent_interval'    => 180,
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],

    'v3toys/agents/messages-update' =>
    [
        'description'       => 'Обновление статусов заявок из v3toys',
        'agent_interval'    => 180,
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],




    
    'v3toys/properties/load' =>
    [
        'description'       => 'Обновление свойств у товаров (только у которых нет)',
        'agent_interval'    => 600, //1 раз в 10 минут, для новых товаров обновление свойств
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],

    'v3toys/properties/load 1' =>
    [
        'description'       => 'Обновление свойств по всем товарам',
        'agent_interval'    => 604800, //1 раз в неделю актуализация свойств
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],

    'v3toys/prices/load' =>
    [
        'description'       => 'Обновление цен и наличия',
        'agent_interval'    => 10600,
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],

    'v3toys/products/load' =>
    [
        'description'       => 'Получение и обновление данных по товарам аффилиата',
        'agent_interval'    => 3600,
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ],


];