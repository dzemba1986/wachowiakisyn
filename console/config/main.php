<?php

use common\models\rmq\ErrorJob;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queueSoaSeu', 'queueSeuSoa'],
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
        'queueSoaSeu' => [
            'class' => \yii\queue\amqp_interop\Queue::class,
            'host' => 'rabbitmq.wtvk.pl',
            'port' => 5672,
            'user' => 'daniel',
            'password' => '4@l@$hn140v',
            'queueName' => 'SOA-SEU',
            'exchangeName' => 'SOA-SEU',
            'driver' => yii\queue\amqp_interop\Queue::ENQUEUE_AMQP_LIB,
            'strictJobType' => false,
            'serializer' => common\models\rmq\JsonSerializer::class,
            'on beforeExec' => function ($event) {
                $job = $event->job;
                $isValid = $job->validate();
                $job->save(false);
                if (!$isValid) {
                    $keyError = array_key_first($job->firstErrors);
                    $desc = 'Błąd walidacji - [' . $keyError . ' - ' . $job->firstErrors[$keyError] . ']';
                    $error = \Yii::createObject([
                        'class' => ErrorJob::class,
                    ], [$job->case_id, $desc]);
                    if ($error->save()) \Yii::$app->queueSeuSoa->push($error);
                    
                    exit();
                }
            },
        ],
        'queueSeuSoa' => [
            'class' => \yii\queue\amqp_interop\Queue::class,
            'host' => 'rabbitmq.wtvk.pl',
            'port' => 5672,
            'user' => 'daniel',
            'password' => '4@l@$hn140v',
            'queueName' => 'SEU-SOA',
            'exchangeName' => 'SEU-SOA',
            'driver' => yii\queue\amqp_interop\Queue::ENQUEUE_AMQP_LIB,
            'strictJobType' => false,
            'serializer' => common\models\rmq\JsonSerializer::class,
            'on beforePush' => function ($event) {
                $job = $event->job;
                $job->setParams();
                if (!$job->save()) {
                    $desc = 'Wewnętrzny błąd po stronie SEU';
                    $error = \Yii::createObject([
                        'class' => ErrorJob::class,
                    ], [$job->case_id, $desc]);
                    if ($error->save()) \Yii::$app->queueSeuSoa->push($error);
                
                    exit();
                }
            },
        ],
    ],
    'params' => $params,
];
