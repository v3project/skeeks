<?php
return
[
    'v3toys/agents/products-update' =>
    [
        'description'       => 'Обновление цен и налчия товаров',
        'agent_interval'    => 3600*0.5, //раз в 30 минут
        'next_exec_at'      => \Yii::$app->formatter->asTimestamp(time()) + 3600*0.5,
        'is_period'         => 'N'
    ]
];