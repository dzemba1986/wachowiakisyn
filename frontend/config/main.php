<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'soa/connection/index',
    'timeZone' => 'Europe/Warsaw',
	'modules' => [
		'gridview' => [
			'class' => '\kartik\grid\Module',
			'i18n' => [
				'class' => 'yii\i18n\PhpMessageSource',
				'basePath' => '@kvgrid/messages',
				'forceTranslation' => true,
				'sourceLanguage' => 'pl',
			]
		],
        'crm' => [
            'class' => 'frontend\modules\crm\Module'
        ],
        'history' => [
            'class' => 'frontend\modules\history\Module'
        ],
        'report' => [
            'class' => 'frontend\modules\report\Module'
        ],
        'seu' => [
            'class' => 'frontend\modules\seu\Module',
        ],
        'soa' => [
            'class' => 'frontend\modules\soa\Module',
            'defaultRoute' => 'connection/index'
        ],
	],
    'components' => [
        'request' => [
            'baseUrl' => '',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//             'enableStrictParsing' => false,
        ],
        'urlManagerBackend' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '/backend/',
            'showScriptName' => false,
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'jsOptions' => ['position' => \yii\web\View::POS_BEGIN],
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
