<?php
$params = array_merge(
		require(__DIR__ . '/../../common/config/params.php'),
		require(__DIR__ . '/../../common/config/params-local.php'),
		require(__DIR__ . '/params.php'),
		require(__DIR__ . '/params-local.php')
		);

return [
		'id' => 'rest-api',
		'basePath' => dirname(__DIR__),
		'bootstrap' => ['log'],
		'modules' => [
				'v1' => [
// 						'basePath' => dirname(__DIR__) . '/../../',
						'class' => 'rest\versions\v1\RestModule' 
				],
// 				'v2' => [
// 						'class' => 'rest\versions\v2\RestModule'
// 				],
		],
		'components' => [
				'user' => [
						'identityClass' => 'common\models\User',
						'enableSession' => false,
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
							'application/xml' => [
								'bobchengbin\Yii2XmlRequestParser\XmlRequestParser',
								'priority' => 'tag', // the default value is 'tag', you can set 'attribute' value
							]		
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