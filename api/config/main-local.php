<?php

return [
    'components' => [
    	'db' => [
    		'class' => 'yii\db\Connection',
    		'dsn' => 'pgsql:host=10.111.233.7;dbname=wachowiak',
    		'username' => 'postgres',
    		'password' => 'cz3g05zuk@5z',
    		'charset' => 'utf8',
    		'schemaMap' => [
    			'pgsql'=> [
    				'class' => 'yii\db\pgsql\Schema',
    				'defaultSchema' => 'public',
    			]
    		],
    	    //'enableSchemaCache' => true,
    	],
    	'dbSOA' => [
    		'class' => 'yii\db\Connection',
    		'dsn' => 'pgsql:host=10.111.222.41;dbname=wtvk',
    		'username' => 'serwis',
    		'password' => '$ynchr0n1z@cj@',
    		'charset' => 'utf8',
    		'schemaMap' => [
    			'pgsql'=> [
    				'class'=>'yii\db\pgsql\Schema',
    				'defaultSchema' => 'public',
    			]
    		],
    	],
    	'mailer' => [
    		'class' => 'yii\swiftmailer\Mailer',
    		'viewPath' => '@common/mail',
    		'transport' => [
    			'class' => 'Swift_MailTransport',
    		],
    		'useFileTransport' => false,
    	],
    ],
//         'mailer' => [
//             'class' => 'yii\swiftmailer\Mailer',
//             'viewPath' => '@common/mail',
//             // send all mails to a file by default. You have to set
//             // 'useFileTransport' to false and configure a transport
//             // for the mailer to send real emails.
//             'useFileTransport' => true,
//         ],
//     ],
];