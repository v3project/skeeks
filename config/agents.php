<?php
return
[
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
        'agent_interval'    => 60,
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 60,
        'is_period'         => 'N'
    ]
];