<?php

namespace frontend\modules\soa\controllers;

use common\models\soa\Connection;
use common\models\soa\ConnectionSearch;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ConnectionController extends Controller {
    
    public function behaviors() {
        
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

    public function actionIndex($mode = 'todo') {
        
        $searchModel = new ConnectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        if ($mode == 'todo') {
            $dataProvider->sort->defaultOrder = ['start_at' => SORT_DESC];
            $dataProvider->query->andWhere([
                'connection.nocontract' => false
            ])->andWhere([
                'or',
                ['and', ['or', ['conf_at' => null], ['pay_at' => null]], ['connection.type_id' => [1,3]]],
                ['and', ['pay_at' => null], ['connection.type_id' => 2]]
            ])->andWhere(['connection.close_at' => null]);
//         var_dump($dataProvider); exit();
            
            return $this->render('todo', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } elseif ($mode == 'all') {
            return $this->render('grid_all', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } elseif ($mode == 'noboa') {
            $dataProvider->sort->defaultOrder = ['pay_date' => SORT_ASC];
            $dataProvider->query->andWhere(['and', ['synch_date' => null], ['nocontract' => false], ['vip' => false], ['is not', 'pay_date', null]]);
            
            return $this->render('grid_noboa', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    
    public function actionIndexByAddress($address_id) {
        
        $searchModel = new ConnectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->sort->defaultOrder = ['start_at' => SORT_DESC];
        $dataProvider->query->where([
            'nocontract' => false,
            'address_id' => $address_id
        ]);
            
        return $this->render('index_by_address', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
            ['is not', 'host_id', null]
        ])->count();
        
        $disableDevicesList = (!$allConnections && !$connection->host_id && $connection->type_id <> 2) || ($connection->type_id == 2 && (Yii::$app->user->id == 24 || Yii::$app->user->id == 22)) ? false : true;

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
                'disableDevicesList' => $disableDevicesList,
                'jsonType' => json_encode($connection->posibleParentTypeIds),
                'deviceListUrl' => '/seu/device/list-from-tree',
                'portListUrl' => '/seu/link/list-port'
            ]);
        }
    }
    
    public function actionSync($id)
    {
        $connection = $this->findModel($id);
        $connection->scenario = Connection::SCENARIO_UPDATE;
        
        $connection->synch_date = date('Y-m-d H:i:s');
        
        if (!$connection->save()) return 1;
        else return 0;
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
