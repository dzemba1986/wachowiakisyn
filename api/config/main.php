<?php
$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../common/config/params-local.php'),
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

return [
	'id' => 'api',
	'basePath' => dirname(__DIR__),
	'bootstrap' => ['log'],
	'modules' => [
		'v1' => [
			'basePath' => dirname(__DIR__) . '/../../',
			'class' => 'app\versions\v1\RestModule' 
			],
	],
	'components' => [
		'user' => [
			'identityClass' => 'common\models\User',
			'enableSession' => false,
			'enableAutoLogin'  => false
		],
		'response' => [
			'format' => yii\web\Response::FORMAT_JSON,
			'charset' => 'UTF-8',
		],
		'log' => [
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['error', 'warning'],
				],
			],
		],
		'request' => [
			'class' => '\yii\web\Request',
			'enableCookieValidation' => false,
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			],
		],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'enableStrictParsing' => true,
			'showScriptName' => false,
			'rules' => [
				['class' => 'yii\rest\UrlRule', 'controller' => 'v1/connection'],
			],
		],
	],
	'params' => $params,
];