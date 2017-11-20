<?php

namespace backend\modules\task\controllers;

use backend\models\Address;
use backend\models\Connection;
use backend\modules\task\models\DeviceTask;
use backend\modules\task\models\DeviceTaskSearch;
use backend\modules\task\models\InstallTask;
use backend\modules\task\models\Task;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\web\Controller;

class DeviceTaskController extends Controller
{
    public function actionIndex($mode = 'todo')
    {
        $searchModel = new DeviceTaskSearch();
        
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        if ($mode == 'todo') {
        	$dataProvider->query->andWhere([
        		'and',
        		['close' => null],	
        		['is', "{$searchModel->tableName()}.status", null],
        		['is not', 'device_id', null]
        	])->orderBy('create');
        	
        } elseif ($mode == 'close') {
        	$dataProvider->query->joinWith([
        		'closeUser' => function ($q) {
        			$q->from(['u' => User::tableName()]);
    			}
        	])->andWhere([
        		'and',	
        		['is not', 'close', null],
        		['is not', 'device_id', null]
        		//'is not', 'close_user', null,
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

    public function actionCreate(){
    
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
    				
    				if ($address->load($request->post()) && is_null($connection)){	//zadanie bez LP
    					if (!$address->save()) 
    						throw new Exception('Problem z zapisem adresu');
    					
    					$task->address_id = $address->id;
    					
    					if (!$task->save())
    						throw new Exception('Problem z zapisem zadania');
    					
    				} else {	//zadanie z LP
    					$task->address_id = $connection->address;
    					
    					if ($task->save()){
    						$connection->task = $task->id;
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
    				if ($task->beforeValidate() && !$task->save())
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
    		$task->scenario = DeviceTask::SCENARIO_CLOSE;
    		
    		if ($task->load($request->post())){
    			try {
    				if (!$task->save())
    					throw new Exception('Problem z zamknięciem zadania');
    			} catch (Exception $e) {
    				print_r($task->errors);
    				echo $e->getMessage();
    				return 0;
    			}
    			
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
        if (($model = DeviceTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
