<?php

namespace backend\modules\address\controllers;

use backend\modules\address\models\Address;
use backend\modules\address\models\AddressSearch;
use backend\modules\address\models\AddressShort;
use backend\modules\address\models\AddressShortSearch;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AddressController extends Controller
{
    public function behaviors() {
        
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules'	=> [
                    [
                        'allow' => true,
                        'actions' => [
                            'index', 'list', 'create', 'teryt-list', 'update-short', 'delete', 'view'
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
                'only' => ['create', 'teryt-list', 'update-short']
            ],
        ];
    }

    public function actionIndex() {
        
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionList() {
        
    	$searchModel = new AddressShortSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	
    	return $this->render('list', [
    		'searchModel' => $searchModel,
    		'dataProvider' => $dataProvider,
    	]);
    }
    
    public function actionCreate() {
        
    	$request = Yii::$app->request;
    	
		$model = new AddressShort();
		
		try {
			if ($model->load($request->post())) {
				$model->config = (int) $model->config;	//z POST'a pobiera jako string
				if ($model->save())
					return 1;
				else
					return var_dump($model->firstErrors);
			} else {
				return $this->renderAjax('create_short', [
					'model' => $model
				]);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
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
    
    public function actionUpdateShort($id) {
        
        $request = Yii::$app->request;
        
        $model = AddressShort::findOne($id);
        
        try {
            if ($model->load($request->post())) {
                if ($model->save()) return 1;
                else return 0;
            } else {
                return $this->renderAjax('update_short', [
                    'model' => $model
                ]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function actionTerytList($q) {
    	
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	
    	$out = ['results' => ['id' => '', 'concat' => '']];
    	
    	if (!is_null($q)) {
    		
    		// ...AS id... potrzebne by była możliwość wybrania rezultatów
    		$query = new Query();
    		$query->select(['sym_ul AS id', new \yii\db\Expression("
	    		CONCAT(cecha, ' ', nazwa_2, ' ', nazwa_1)
	    	"), 'sym', 'gmi', 'cecha', 'nazwa_1', 'nazwa_2'])
	    	->from('ulic')
	    	->where(['and', ['woj' => '30'], ['pow' => '64'], ['rodz_gmi' => '9'], ['like', 'nazwa_1', $q]])
    		->limit(10);
	    		
			$command = $query->createCommand();
	 		$data = $command->queryAll();
	    	$out['results'] = array_values($data);
    	}
    	
    	return $out;
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
