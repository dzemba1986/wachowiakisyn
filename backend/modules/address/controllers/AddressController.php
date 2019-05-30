<?php

namespace backend\modules\address\controllers;

use backend\modules\address\models\Teryt;
use backend\modules\address\models\TerytSearch;
use common\models\address\Address;
use common\models\address\AddressSearch;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use backend\modules\address\models\ServiceRangeSearch;
use backend\modules\address\models\ServiceRange;

class AddressController extends Controller {
    
    public function behaviors() {
        
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules'	=> [
                    [
                        'allow' => true,
                        'actions' => [
                            'index', 'list', 'service-by-address', 'address-by-street', 'create-street', 'create-service', 'view', 'update-teryt'
                        ],
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post']
                ],
            ],
            [
                'class' => AjaxFilter::className(),
                'only' => ['create-new-street', 'list', 'index2']
            ],
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['list'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }
    
    public function actionList($q) {
        
        $out = ['results' => ['id' => '', 'concat' => '']];
        
        if (!is_null($q)) {
            $query = new Query();
            $query->select(['id' => 'sym_ul', new Expression("CONCAT(cecha, ' ', nazwa_2, ' ', nazwa_1)")])
            ->from('ulic')->where(['and', ['woj' => '30'], ['pow' => '64'], ['rodz_gmi' => '9'], ['like', 'lower(nazwa_1)', mb_strtolower($q, 'UTF-8')]])->limit(50);
            
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        
        return $out;
    }

    
    public function actionIndex() {
        
    	$searchModel = new TerytSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	
    	return $this->render('index', [
    		'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    	]);
    }
    
    public function actionAddressByStreet($t_ulica) {
        
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->query->andWhere([
            't_ulica' => $t_ulica
        ]);

        return $this->renderAjax('address', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionServiceByAddress() {
        
        $searchModel = new ServiceRangeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('service', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionCreateStreet() {
        
    	$request = Yii::$app->request;
		$model = new Teryt();
		
		if ($model->load($request->post())) {
			if ($model->save())
				return 1;
			else
				return var_dump($model->firstErrors);
		} else {
			return $this->renderAjax('create_street', [
				'model' => $model
			]);
		}
    }
    
    public function actionCreateService() {
        
    	$request = Yii::$app->request;
        $service = Yii::createObject(ServiceRange::class);
        
        if ($service->load($request->post())) {
            if ($service->save()) return 1;
            else return var_dump($service->firstErrors);
        } else {
            return $this->renderAjax('create_service', [
                'service' => $service,
            ]);
        }
    }

    public function actionUpdateService($id) {
        
        $service = ServiceRange::findOne($id);
        
        return $this->renderAjax('update_service', [
            'service' => $service,
        ]);
    }

    
    public function actionView($id) {
        
        $address = $this->findModel($id);
        
        return $this->renderAjax('view', [
            'address' => $address,
        ]);
    }
    
    public function actionUpdate($id) {
        
        $request = Yii::$app->request;
        
        $address = Address::findOne($id);
        
        try {
            if ($address->load($request->post())) {
                if ($address->save()) return 1;
                else return 0;
            } else {
                return $this->renderAjax('update', [
                    'address' => $address
                ]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function actionUpdateTeryt($id) {
        
        $request = Yii::$app->request;
        $model = Teryt::findOne($id);
        
        try {
            if ($model->load($request->post())) {
                if ($model->save()) return 1;
                else return 0;
            } else {
                return $this->renderAjax('update_teryt', [
                    'model' => $model
                ]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    

    public function actionDelete($id) {
        
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id) {
        
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
