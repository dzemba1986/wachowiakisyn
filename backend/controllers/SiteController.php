<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use backend\models\Dhcp;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'get-menu', 'php-info'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionPhpInfo()
    {
    	phpinfo();
    }
    
    public function actionDhcp()
    {
        //return $this->render('index');
        Dhcp::generateFile();
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    public function actionGetMenu($controler)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($controler == 'connection' || $controler == 'installation' || $controler == 'task' || $controler == 'address'
        	|| $controler == 'raport'){
            $menu = [
            	[
            		'html' => '<a href=' . Url::to(['address/index']) . '>Adresy</a>'
            	],
                [   
                    'html' => 'Połączenia',
                    'items' => [
                        [
                            'html' => '<a href=' . Url::to(['connection/index', 'mode' => 'nopay']) . '>Niepłacący</a>'
                        ],
                        [
                            'html' => '<a href=' . Url::to(['connection/index', 'mode' => 'install']) . '>Bez kabla</a>'
                        ],
                        [
                            'html' => '<a href=' . Url::to(['connection/index', 'mode' => 'conf']) . '>Do konfiguracji</a>',
                        ],
                        [
                            'html' => '<a href=' . Url::to(['connection/index', 'mode' => 'off']) . '>Nieaktywne</a>',
                        ],
                        [
                            'html' => '<a href=' . Url::to(['connection/index', 'mode' => 'pay']) . '>Płacący</a>'
                        ],
                        [
                            'html' => '<a href=' . Url::to(['connection/index', 'mode' => 'all']) . '>Wszystkie</a>'
                        ],
                    ]
                ],
                [
                    'html' => '<a href=' . Url::toRoute('installation/index') . '>Instalacje</a>',
                ],
                [
	                'html' => 'Zadania',
	                'items' => [
                		[
                			'html' => '<a href=' . Url::to(['task/index', 'mode' => 'todo']) . '>Do zrobienia</a>'
                		],
	                	[
	                		'html' => '<a href=' . Url::to(['task/index', 'mode' => 'close']) . '>Zrobione</a>'
	                	],
	                ],		
                ],
            	[
            		'html' => 'Zestawienia',
            		'items' => [
            			[
            				'html' => '<a href=' . Url::to(['raport/connection']) . '>Podłączenia</a>'
            			],
            			[
            				'html' => '<a href=' . Url::to(['raport/installation']) . '>Instalacje</a>'
            			],
            		],
            	],
            	[
            		'html' => 'Boa',
            		'items' => [
            				[
            						'html' => '<a href=' . Url::to(['connection/index', 'mode' => 'noboa']) . '>Niezaksięgowane</a>'
            				],
            				[
            						'html' => '<a href=' . Url::to(['connection/index', 'mode' => 'boa']) . '>Zaksiegowane</a>'
            				],
            		],
            	],
            ];
        }
        else{
            $menu = [
                [   
                    'html' => '<a href=' . Url::toRoute(['tree/index']) . '>Drzewo urządzeń</a>',                 
                ],
            	[
            		'html' => 'Sieć',
            		'items' => [
            				[
            						'html' => '<a href=' . Url::to(['vlan/grid']) . '>Adresacja</a>'
            				],
            				[
            						'html' => '<a href=' . Url::to(['dhcp/index']) . '>DHCP</a>'
            				],
            		],
            	],
                [
                    'html' => '<a href=' . Url::to(['store/index']) . '>Magazyn</a>',
                ],                
            ];
        }
        
        //var_dump(Yii::$app->controller->id);
    	return $menu;
    }
}
