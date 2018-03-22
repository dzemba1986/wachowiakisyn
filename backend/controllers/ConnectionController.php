<?php

namespace backend\controllers;

use backend\models\Connection;
use backend\models\ConnectionSearch;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use backend\models\History;
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
        				'actions' => ['create', 'delete', 'index', 'update', 'view', 'sync', 'close', 'history'],
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

    public function actionIndex($mode = 'nopay')
    {
        $searchModel = new ConnectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        switch ($mode){
        	case 'nopay':
        		$dataProvider->sort = ['defaultOrder' => [
        			'start_date' => SORT_DESC, 
        		]];
        		$dataProvider->query->joinWith('task')->andWhere([
        			'pay_date' => null,
        			'close_date' => null
        		]);
        		break;
        	case 'install':
        		$dataProvider->query->joinWith('task')->andWhere([
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

    public function actionView($id)
    {
    	if(Yii::$app->request->isAjax)
            $model = $this->findModel($id);
    	    
            return $this->renderAjax('view', [
                'model' => $model,
                'installations' => $model->installations, 
            ]);
    }

    public function actionCreate()
    {
        //TODO należy udostepnić dodawanie ankiet przez serwis
        $model = new Connection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id) {
        
        $request = Yii::$app->request;
        
        if ($request->isAjax){
            $connection = $this->findModel($id);
            $connection->scenario = Connection::SCENARIO_UPDATE;
            
            $allConnections = Connection::find()->where([
                'and', 
                ['<>', 'type_id', $connection->type_id], 
                ['address_id' => $connection->address_id], 
                ['is not', 'host_id', null]])
            ->count();
            
            if ($connection->load($request->post())) {
                if (!empty($connection->close_date) && is_null($connection->oldAttributes['close_date'])) {
                    $connection->close_user = \Yii::$app->user->identity->id;
                    $connection->close_date = $connection->close_date . ' ' . date('H:i:s');
                }
                try {
                    if (!$connection->save()) throw new Exception('Problem z zapisem połączenia');
                } catch (\Throwable $t) {
                    var_dump($connection->errors);
                    var_dump($t->getMessage());
                    exit();
                }
                
                return 1;
            } else {
                return $this->renderAjax('update', [
                    'connection' => $connection,
                    'allConnections' => $allConnections,
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
    
    function actionHistory($id) {
        
        $histories = History::find()->joinWith('user')->select('history.created_at, last_name, desc')->where(['connection_id' => $id])->asArray()->all();
        
        return $this->renderAjax('history', [
            'histories' => $histories
        ]);
    }
    
    function actionClose($id) {
        
        $request = Yii::$app->request;
        $connection = $this->findModel($id);
        $hostId = $connection->host->id;
        
        if ($request->isPost) {
            $transaction = \Yii::$app->db->beginTransaction();
            
            $connection->close_user = Yii::$app->user->identity->id;
            $connection->close_date = date('Y-m-d H:i:s');
            $connection->host_id = null;
            
            try {
                if (!$connection->save()) throw new Exception('Błąd zamknięcia umowy');
                
                $transaction->commit();
                return 1;
            } catch (\Throwable $t) {
                $transaction->rollBack();
                var_dump($t->getMessage());
                var_dump($connection->errors);
                exit();
            }
        } else {
            return $this->renderAjax('close', [
                'hostId' => $hostId,
            ]);
        }
        
    }

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
