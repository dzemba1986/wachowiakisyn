<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'apiIcingaClient' => [
            'class' => 'yii\httpclient\Client',
            'baseUrl' => 'https://10.111.233.4:5665/v1',
            'requestConfig' => ['format' => 'json', 'options' => ['sslverify_peer' => false, 'sslverify_peer_name' => false]], //['sslverify_peer' => false, 'sslverify_peer_name' => false],
        ],
    ],
	'aliases' => [
		'@common' => dirname(__DIR__),
		'@frontend' => dirname(dirname(__DIR__)) . '/frontend',
		'@backend' => dirname(dirname(__DIR__)) . '/backend',
		'@console' => dirname(dirname(__DIR__)) . '/console',
	    '@components' => dirname(dirname(__DIR__)) . '/components',
		'@loganalyzer' => 'http://172.20.4.17:701',
	    '@bower' => '@vendor/bower-asset',
	    '@npm'   => '@vendor/npm-asset',
	]	
];
