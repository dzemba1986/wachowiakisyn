<?php

namespace backend\controllers;

use Yii;
use backend\models\Connection;
use backend\models\ConnectionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Device;
use backend\models\Dhcp;
use yii\widgets\ActiveForm;
/**
 * ConnectionController implements the CRUD actions for Connection model.
 */
class ConnectionController extends Controller
{
    public function behaviors()
    {
        return [
        	'access' => [
        		'class' => AccessControl::className(),
        		//'only' => ['create', 'delete', 'index', 'update'],
        		'rules'	=> [
        			[
        				'allow' => true,
        				'actions' => ['create', 'delete', 'index', 'update', 'view', 'sync', 'validation'],
        				'roles' => ['@']	
        			]	
        		]
        	],	
        		
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Connection models.
     * @return mixed
     */
    public function actionIndex($mode = 'nopay')
    {
        $searchModel = new ConnectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        switch ($mode){
        	case 'nopay':
        		$dataProvider->sort = ['defaultOrder' => [
        			'start_date' => SORT_DESC, 
        		]];
        		$dataProvider->query->joinWith('modelTask')->andWhere([
        			'pay_date' => null,
        			'connection.close_date' => null
        		]);
        		break;
        	case 'install':
        		$dataProvider->query->joinWith('modelTask')->andWhere([
        			'wire' => 0, 
        			'connection.close_date' => null
        		]);
        		break;
        	case 'conf':
        		$dataProvider->query->andWhere([
        			'conf_date' => NULL, 
        			'close_date' => NULL, 
        			Connection::tableName().'.type' => 1 
        		])->andWhere(['and', ['>', 'wire', 0], ['nocontract' => false], ['is', 'host', null]]);
        		break;
        	case 'off':
        		$dataProvider->sort = ['defaultOrder' => ['close_date' => SORT_ASC]];
        		$dataProvider->query->andWhere(['is not', 'close_date', null]);
        		break;
        	case 'pay':
        		$dataProvider->query->andWhere(['and', ['is not', 'pay_date', null], ['close_date' => null]]);
        		break;
        	case 'noboa':
        		$dataProvider->query->andWhere(['synch_date' => null, 'nocontract' => false, 'vip' => false])->andWhere(['is not', 'pay_date', null]);
        		break;
        	case 'boa':
        		$dataProvider->query->andWhere(['close_date' => null, 'nocontract' => false, 'vip' => false])->andWhere(['is not', 'synch_date', null]);
        		break;
        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mode' => $mode,
        ]);
    }

    /**
     * Displays a single Connection model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
    	if(Yii::$app->request->isAjax)
    	
            return $this->renderAjax('view', [
                'modelConnection' => $this->findModel($id),
            ]);
    }

    /**
     * Creates a new Connection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Connection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Connection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $modelConnection = $this->findModel($id);
        $modelConnection->scenario = Connection::SCENARIO_UPDATE;
        
        $request = Yii::$app->request;

        if ($request->isAjax){
	        if ($modelConnection->load(Yii::$app->request->post())) {
	        	
	        	$transaction = Yii::$app->getDb()->beginTransaction();
	        	
	        	try {
	        		if (is_null($modelConnection->getOldAttribute('cloase_date')) && $modelConnection->close_date){
	        			
	        			if ($modelConnection->host && $modelConnection->type == 1) {
		        			
		        			$modelDevice = Device::findOne($modelConnection->host);
		        			
		        			$subnet = $modelDevice->modelIps[0]->modelSubnet->id;
		        			
		        			$modelDevice->modelTree[0]->delete();
		        			$modelDevice->modelIps[0]->delete();
		        			$modelDevice->delete();
		        			
		        			$modelConnection->host = null;
		        			
		        			Dhcp::generateFile($subnet);
	        			}
	        		}
	        		
	        		$modelConnection->close_user = Yii::$app->user->identity->id;
	        		
	        		if(!($modelConnection->save()))
	        			throw new Exception('Problem z zapisem połączenia');
	        		
	        		$transaction->commit();
	        		
        			return 1;
	        	} catch (Exception $e) {
	        		$transaction->rollBack();
	        		return 0;
	        	}
	        } else {
	            return $this->renderAjax('update', [
	                'modelConnection' => $modelConnection,
	            ]);
	        }
        }
    }
    
    public function actionSync($id)
    {
    	$modelConnection = $this->findModel($id);
    	$modelConnection->scenario = Connection::SCENARIO_UPDATE;
    	
    	$request = Yii::$app->request;

    	if ($request->isAjax){
    		
    		$modelConnection->synch_date = date('Y-m-d H:i:s');
    		
    		try {
    			if(!($modelConnection->save()))
    				throw new Exception('Problem z zapisem połączenia');
    			
    			return 1;	
    		} catch (Exception $e) {
    			return 0;
    		}
    	}
    }
    
    public function actionValidation($type = 5){
    		
    	$modelConnection = new Connection();
    
    	$request = Yii::$app->request;
    
    	if ($request->isAjax && $modelConnection->load($request->post())) {
    			
    		//var_dump($modelDevice); exit();
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		return ActiveForm::validate($modelConnection, 'mac');
    	}
    }

    /**
     * Deletes an existing Connection model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    protected function findModel($id)
    {
        if (($model = Connection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
