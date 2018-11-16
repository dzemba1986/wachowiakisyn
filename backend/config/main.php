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
    'defaultRoute' => '/address/address/list',
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
            'class' => 'backend\modules\seu\Module',
            'controllerMap' => [
                'device' => 'backend\modules\seu\controllers\devices\DeviceController',
                'camera' => 'backend\modules\seu\controllers\devices\CameraController',
                'gateway-voip' => 'backend\modules\seu\controllers\devices\GatewayVoipController',
                'host-ethernet' => 'backend\modules\seu\controllers\devices\HostEthernetController',
                'host-rfog' => 'backend\modules\seu\controllers\devices\HostRfogController',
                'media-converter' => 'backend\modules\seu\controllers\devices\MediaConverterController',
                'optical-amplifier' => 'backend\modules\seu\controllers\devices\OpticalAmplifierController',
                'optical-splitter' => 'backend\modules\seu\controllers\devices\OpticalSplitterController',
                'optical-transmitter' => 'backend\modules\seu\controllers\devices\OpticalTransmitterController',
                'radio' => 'backend\modules\seu\controllers\devices\RadioController',
                'router' => 'backend\modules\seu\controllers\devices\RouterController',
                'server' => 'backend\modules\seu\controllers\devices\ServerController',
                'swith' => 'backend\modules\seu\controllers\devices\SwithController',
                'ups' => 'backend\modules\seu\controllers\devices\UpsController',
                'virtual' => 'backend\modules\seu\controllers\devices\VirtualController',
                'dhcp-value' => 'backend\modules\seu\controllers\network\DhcpValueController',
                'ip' => 'backend\modules\seu\controllers\network\IpController',
                'subnet' => 'backend\modules\seu\controllers\network\SubnetController',
                'vlan' => 'backend\modules\seu\controllers\network\VlanController',
            ]
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
