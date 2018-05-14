<?php

namespace backend\controllers;

use backend\models\Connection;
use backend\models\ConnectionSearch;
use backend\models\History;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
        		'rules'	=> [
        			[
        				'allow' => true,
        				'actions' => ['create', 'delete', 'index', 'update', 'view', 'sync', 'close', 'history'],
        				'roles' => ['@']	
        			]	
        		]
        	],	
            [
                'class' => AjaxFilter::className(),
                'only' => ['view', 'update', 'sync']
            ],
        ];
    }

    public function actionIndex($mode = 'todo')
    {
        $searchModel = new ConnectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        if ($mode == 'todo') {
            $dataProvider->sort->defaultOrder = ['start_date' => SORT_DESC];
            $dataProvider->query->joinWith('task')->andWhere([
                'connection.nocontract' => false
            ])->andWhere([
                'or',
                ['and', ['or', ['conf_date' => null], ['pay_date' => null]], ['connection.type_id' => [1,3]]],
                ['and', ['pay_date' => null], ['connection.type_id' => 2]]
            ])->andWhere(['close_date' => null]);
            
            return $this->render('grid_todo', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } elseif ($mode == 'all') {
            return $this->render('grid_all', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } 
    }

    public function actionView($id) {
        
        $model = $this->findModel($id);
	    
        return $this->renderAjax('view', [
            'model' => $model,
            'installations' => $model->installations, 
        ]);
    }

    public function actionUpdate($id) {
        
        $request = Yii::$app->request;
        
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
    
    function actionHistory($id) {
        
        $histories = History::find()->joinWith('user')->select('history.created_at, created_by, last_name, desc')->where(['connection_id' => $id])->asArray()->all();
        
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

    protected function findModel($id)
    {
        if (($model = Connection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
