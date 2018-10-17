<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
	'defaultRoute' => 'connection/index',
    'timeZone' => 'Europe/Warsaw',
    'bootstrap' => ['log'],
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
    	'task' => [
    		'class' => 'backend\modules\task\Module'	
    	],
        'address' => [
            'class' => 'backend\modules\address\Module'
        ],
        'crm' => [
            'class' => 'backend\modules\crm\Module'
        ],
        'history' => [
            'class' => 'backend\modules\history\Module'
        ],
        'report' => [
            'class' => 'backend\modules\report\Module'
        ],
        'seu' => [
            'class' => 'backend\modules\seu\Module'
        ],
        'soa' => [
            'class' => 'backend\modules\soa\Module'
        ],
    ],
    'components' => [
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
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
