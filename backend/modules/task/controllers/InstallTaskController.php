<?php

namespace backend\modules\task\controllers;

use backend\models\Address;
use backend\models\Connection;
use backend\models\Installation;
use backend\modules\task\models\InstallTask;
use backend\modules\task\models\InstallTaskSearch;
use backend\modules\task\models\Task;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\web\Controller;

class InstallTaskController extends Controller
{
    public function actionIndex($mode = 'todo')
    {
        $searchModel = new InstallTaskSearch();
        
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        if ($mode == 'todo') {
        	$dataProvider->query->andWhere([
        		'and',
        		['close' => null],
        		['device_id' => null],
        		["{$searchModel->tableName()}.status" => null]
        	])->orderBy('start');
        	
        } elseif ($mode == 'close') {
        	$dataProvider->query->joinWith([
        		'closeUser' => function ($q) {
        			$q->from(['u' => User::tableName()]);
    			}
        	])->andWhere([
        		'and',	
        		['is not', 'close', null],
        		['device_id' => null]
        	]);
        	
        	$dataProvider->query->orderBy('close');
        }
        
        //$dataProvider->query->orderBy('start_date, start_time');

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        	'mode' => $mode,	
        ]);
    }

    public function actionViewTaskCalendar($start = null, $end = null, $_ = null){
    	 
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
    	$tasks = InstallTask::find()->select([InstallTask::tableName().'.id', 'start', 'end', 'description', 'address_id', 'color'])
	    	->joinWith('address')
	    	->where(['between', 'start', $start, $end])
	    	->andWhere(['or', ['status' => true], ['is', 'status', null]])
	    	->orderBy('start')->asArray()->all();
        
        $tasks = array_map(function($task) {
            return array(
                'id' => $task['id'],
                'start' => $task['start'],
                'end' => $task['end'],
                'title' => Address::findOne($task['address_id'])->toString(true),
            	'description' => $task['description'],
            	'color' => $task['color']
            );
        }, $tasks);
        
        return $tasks;
    }
    
    public function actionViewCalendar($connectionId = null){
    	
    	$session = \Yii::$app->session;
    	
    	if (!is_null($connectionId))
    		$session->set('connectionId', $connectionId);
    	else 
    		$session->remove('connectionId');
    	
        if(Yii::$app->request->isAjax){        
			
            return $this->renderAjax('calendar');
        }
    }
    
    public function actionCreate($timestamp){
    
    	$request = \Yii::$app->request;
    	$session = \Yii::$app->session;
    	
    	if ($request->isAjax){
    		
    		$task = new InstallTask(['scenario' => InstallTask::SCENARIO_CREATE]);
    		$address = new Address();
    		
    		$session->has('connectionId') ? $connection = Connection::findOne($session->get('connectionId')) : $connection = null;
    		
    		$timestamp = $timestamp / 1000;	//odcinam milisekundy
    		
    		if ($task->load($request->post())){
    		
    			$transaction = Yii::$app->getDb()->beginTransaction();
    			
    			try {
    				$task->add_user = Yii::$app->user->id;
    				$task->color = $task->getColor();
    				$task->start = $task->start_date . ' ' . $task->start_time . ':00';
    				$task->end = $task->start_date . ' ' . $task->end_time . ':00';
    				
    				if ($address->load($request->post()) && is_null($connection)){	//zadanie bez LP
    					if (!$address->save()) 
    						throw new Exception('Problem z zapisem adresu');
    					
    					$task->address_id = $address->id;
    					
    					if (!$task->save())
    						throw new Exception('Problem z zapisem zadania');
    					
    				} else {	//zadanie z LP
    					$task->address_id = $connection->address;
    					
    					if ($task->save()){
    						$connection->task_id = $task->id;
    						if (!$connection->save())
    							throw new Exception('Problem z zapisem połączenia');
    					} else
    						throw new Exception('Problem z zapisem zadania');
    				}
    			} catch (Exception $e) {
    				$transaction->rollBack();
    				print_r($address->errors);
    				print_r($task->errors);
    				isset($connection) ? print_r($connection->errors) : null;
    				echo $e->getMessage();
    				return 0;
    			}
				
    			$transaction->commit();
    			return 1;
    		} else {
    			$task->start_date = date('Y-m-d', $timestamp);
    			$task->start_time = date('H:i', $timestamp);
    			$task->end_time = date('H:i', $timestamp + 3600);
    			
    			if (is_object($connection)){
    				$task->phone = $connection->phone;
    				$task->type_id = $connection->type;
    				$task->category_id = 1;	//wybieramy katęgorię `instalacja`
    			}
    			
    			return $this->renderAjax('create', [
					'task' => $task,
					'address' => $address,
    				'connection' => $connection
    			]);
    		}
    	}
    }
    
    public function actionUpdate($id)
    {
    	$request = \Yii::$app->request;
    	
    	if ($request->isAjax){
    		$task = $this->findModel($id);
    		$task->scenario = InstallTask::SCENARIO_UPDATE;
    		
    		if ($task->load(Yii::$app->request->post())){
    			
    			try {
    				$task->start = $task->start_date . ' ' . $task->start_time . ':00';
    				$task->end = $task->start_date . ' ' . $task->end_time . ':00';
    				
    				if (!$task->save())
    					throw new Exception('Problem z zapisem zadania');
    			} catch (Exception $e) {
    				print_r($task->errors);
    				echo $e->getMessage();
    				return 0;
    			}

    			return 1;
    					
    		} else {
    			
    			return $this->renderAjax('update', [
    				'task' => $task,
    			]);
    		}
    	}
    }
    
    public function actionClose($id){
    	
    	$request = \Yii::$app->request;
    	
    	if ($request->isAjax){
    		$task = $this->findModel($id);
    		$task->scenario = InstallTask::SCENARIO_CLOSE;
    		
    		if ($task->load($request->post())){
    			
    			$transaction = Yii::$app->db->beginTransaction();
    			$connection = Connection::find()->where(['task_id' => $task->id])->one();
    			
    			if (is_object($connection)){	//zadanie z LP
    				if ($task->status == true){
    					$installation = null;
    					//przeszukaj instalacje których typ odpowiada typowi połączenia
    					foreach ($connection->modelInstallationsByType as $ins){
    						if (!is_null($ins->wire_date) && is_null($ins->socket_date) && is_null($ins->socket_user)){
    							$installation = $ins;	//podstaw gdy coś znalazł
    							break;
    						}
    					}
    					
    					if (is_object($installation)){
    						$installation->scenario = Installation::SCENARIO_SOCKET;
    						
    						try {
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

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['view-calendar']);
    }

    protected function findModel($id)
    {
        if (($model = InstallTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
