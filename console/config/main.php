<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'queue' => [
            'class' => \yii\queue\amqp_interop\Queue::class,
            'host' => 'rabbitmq.wtvk.pl',
            'port' => 5672,
            'user' => 'daniel',
            'password' => '4@l@$hn140v',
            'queueName' => 'testdaniel',
//             'priority' => 10,
//             'driver' => yii\queue\amqp_interop\Queue::ENQUEUE_AMQP_LIB,
//             'strictJobType' => false,
//             'serializer' => \yii\queue\serializers\JsonSerializer::class,
        ],
    ],
    'params' => $params,
];
