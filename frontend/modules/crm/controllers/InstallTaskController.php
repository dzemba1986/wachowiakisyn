<?php

namespace frontend\modules\crm\controllers;

use common\models\address\Address;
use common\models\crm\InstallTask;
use common\models\crm\InstallTaskSearch;
use common\models\crm\Task;
use common\models\soa\Connection;
use common\models\soa\Installation;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\filters\AjaxFilter;
use yii\web\Response;

class InstallTaskController extends TaskController {
    
    public function behaviors() {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['get'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['get', 'create', 'update', 'close'],
            ],
        ];
    }
    
    public function actionIndex() {
        
        $searchModel = new InstallTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        $dataProvider->query->select([
            'task.id', 'start_at', 'end_at', 'task.type_id', 'task.status', 'category_id', 'label_id', 'task.desc', 'address_id', 
            'close_by', 'create_by', 'task.close_at', 'fulfit',
        ]);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGet($start = null, $end = null, $_ = null) {
        
    	$tasks = InstallTask::find()->select([
    	    'task.id', 'start' => 'start_at', 'end' =>'end_at', 'description' => 'desc', 'type' => 'type_id', 'category' => 'tc.name',
    	    'calendar' => new Expression("CASE WHEN receive_by = 1 THEN 'Serwis' ELSE 'Szczurek' END"),
    	    'title' => new Expression("CASE WHEN lokal <> '' THEN teryt.name || dom || dom_szczegol || '/' || lokal ELSE teryt.name || dom || dom_szczegol END")
	    ])->join('INNER JOIN', 'address', 'address.id = address_id')
	       ->join('INNER JOIN', 'teryt', 'teryt.t_ulica = address.t_ulica')
	       ->join('INNER JOIN', 'task_category AS tc', 'tc.id = task.category_id')
    	   ->where(['and', ['between', 'start_at', $start, $end], ['status' => [0,2]]])->orderBy('start_at')->asArray()->all();
    	
	    return $tasks;
    }
    
    public function actionCreate($timestamp) {
    
    	$request = \Yii::$app->request;
    	$session = \Yii::$app->session;
    	
		$task = \Yii::createObject([
		    'class' => InstallTask::class, 
		    'scenario' => InstallTask::SCENARIO_CREATE, 
		]);
		
		if ($conId = $session->get('connectionId')) {
		    $connection = Connection::findOne($conId);
		    $address = $connection->address;
		} else $address = \Yii::createObject(Address::class);
		
		if ($request->isPost) {
        	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			$transaction = Yii::$app->getDb()->beginTransaction();
		    try {
        		if ($address->load($request->post()))
        		    if (!$address->save()) throw new Exception('Problem z zapisem adresu');
                
        	    if ($task->load($request->post())) {
        	        $task->connection_id = $conId;
        	        $task->address_id = $address->id;
        	        
        	        if (!$task->save()) throw new Exception('Problem z zapisem zadania');
        	    }
		    } catch (Exception $e) {
                $transaction->rollBack();
                return [0, [$e->getMessage()]];
		    }
            $transaction->commit();		    
		    return [1, 'Montaż dodany'];
		} else {
		    $task->day = date('Y-m-d', $timestamp);
		    $task->start_time = date('H:i', $timestamp);
		    $task->end_time = date('H:i', $timestamp + 3600);
		    
		    if ($conId) {
		        $task->phone = $connection->phone;
		        $task->category_id = $connection->type;
		    }
		    
		    return $this->renderAjax('create', [
		        'task' => $task,
		        'address' => $address,
		    ]);
		}
    }
    
//     public function actionClose($id) {
    	
//     	$request = \Yii::$app->request;
    	
// 		$task = $this->findModel($id);
// 		$task->scenario = Task::SCENARIO_CLOSE;
    		
// 		if ($task->load($request->post())) {
// 			$transaction = Yii::$app->db->beginTransaction();
			
// 			//gdy montaż jest w ramach umowy i został pozytywnie zakończony
// 			$connections = $task->connection;
// 			if ($connections && $task->fulfit) {
// 			    foreach ($connections as $connection) {
// 			        $connection->pay_at = date('Y-m-d');
// 			    }
// 			}
// 		} else {
// 		    if ($task->install) {
//     			return $this->renderAjax('close_install', [
//     				'task' => $task
//     			]);
// 		    } else {
//     			return $this->renderAjax('close', [
//     				'task' => $task
//     			]);
// 		    }
// 		}
//     }

    public function actionClose_back($id) { //TODO do całkowitej przeróbki na poźniej
    	
    	$request = \Yii::$app->request;
    	
    	if ($request->isAjax){
    		$task = $this->findModel($id);
    		$task->scenario = InstallTask::SCENARIO_CLOSE;
    		
    		if ($task->load($request->post())){
    			
    			$transaction = Yii::$app->db->beginTransaction();
    			$connection = Connection::find()->where(['task_id' => $task->id])->one();
    			
    			if (is_object($connection)){	//zadanie z LP
    				if ($task->status == true) {
    					$installation = null;
    					//przeszukaj instalacje których typ odpowiada typowi połączenia
    					foreach ($connection->getInstallations(true)->all() as $ins){
    						if ($ins->wire_date && !$ins->socket_date && !$ins->socket_user){
    							$installation = $ins;	//podstaw gdy coś znalazł
    							break;
    						}
    					}
    					
    					if (is_object($installation)){
    						$installation->scenario = Installation::SCENARIO_SOCKET;
    						
    						try {
    						    $connection->pay_date = date('Y-m-d');
    							$installation->socket_date = date('Y-m-d');
    							$installation->socket_user = implode(",", $task->installer);
    						
    							if (!$installation->save())
    								throw new Exception('Problem z zapisem instalacji');
    						} catch (Exception $e) {
    							$transaction->rollBack();
    							print_r($installation->errors);
    							echo $e->getMessage();
    							return 0;
    						}
    					} else {
    						return 'Nie znaleziono instalacji z kablem a bez gniazda';
    					}
    				}
    				
    				try {
    					$connection->task_id = null;
    					
    					if (!$connection->save())
    						throw new Exception('Problem z zapisem połączenia');
    				} catch (Exception $e) {
    					$transaction->rollBack();
    					print_r($connection->errors);
    					echo $e->getMessage();
    					return 0;
    				}
    			}
    			
    			try {
    				$task->installer = implode(",", $task->installer);
    				$task->close_user = Yii::$app->user->id;
    				$task->editable = false;
    				$task->color = '#909090';
    				
    				if (!$task->save())
    					throw new Exception('Problem z zapisem montażu');
    					
    			} catch (Exception $e) {
    				$transaction->rollBack();
    				print_r($connection->errors);
    				echo $e->getMessage();
    				return 0;
    			}
    			
    			$transaction->commit();
    			return 1;
    			
    		} else {
    			return $this->renderAjax('close', [
    				'task' => $task
    			]);
    		}
    	}
    }
}
