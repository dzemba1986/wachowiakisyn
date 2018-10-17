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
            'controllerMap' => [
                'device' => 'frontend\modules\seu\controllers\devices\DeviceController',
                'camera' => 'frontend\modules\seu\controllers\devices\CameraController',
                'gateway-voip' => 'frontend\modules\seu\controllers\devices\GatewayVoipController',
                'host-ethernet' => 'frontend\modules\seu\controllers\devices\HostEthernetController',
                'host-rfog' => 'frontend\modules\seu\controllers\devices\HostRfogController',
                'media-converter' => 'frontend\modules\seu\controllers\devices\MediaConverterController',
                'optical-amplifier' => 'frontend\modules\seu\controllers\devices\OpticalAmplifierController',
                'optical-splitter' => 'frontend\modules\seu\controllers\devices\OpticalSplitterController',
                'optical-transmitter' => 'frontend\modules\seu\controllers\devices\OpticalTransmitterController',
                'radio' => 'frontend\modules\seu\controllers\devices\RadioController',
                'router' => 'frontend\modules\seu\controllers\devices\RouterController',
                'server' => 'frontend\modules\seu\controllers\devices\ServerController',
                'swith' => 'frontend\modules\seu\controllers\devices\SwithController',
                'ups' => 'frontend\modules\seu\controllers\devices\UpsController',
                'virtual' => 'frontend\modules\seu\controllers\devices\VirtualController',
                'dhcp-value' => 'frontend\modules\seu\controllers\network\DhcpValueController',
                'ip' => 'frontend\modules\seu\controllers\network\IpController',
                'subnet' => 'frontend\modules\seu\controllers\network\SubnetController',
                'vlan' => 'frontend\modules\seu\controllers\network\VlanController',
            ]
        ],
        'soa' => [
            'class' => 'frontend\modules\soa\Module',
            'defaultRoute' => 'connection/index'
        ],
	],
    'components' => [
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
