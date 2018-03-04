<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
	'aliases' => [
		'@common' => dirname(__DIR__),
		'@frontend' => dirname(dirname(__DIR__)) . '/frontend',
		'@backend' => dirname(dirname(__DIR__)) . '/backend',
		'@console' => dirname(dirname(__DIR__)) . '/console',
		'@loganalyzer' => 'http://172.20.4.17:701'
	]	
];
