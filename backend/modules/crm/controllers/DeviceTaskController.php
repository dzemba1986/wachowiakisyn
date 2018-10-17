<?php

namespace backend\modules\crm\controllers;

use common\models\User;
use common\models\crm\models\DeviceTask;
use common\modules\crm\models\DeviceTaskSearch;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
        		['or', ['task.status' => false], ['task.status' => null]],
        		['is not', 'device_id', null]
        	])->orderBy('status DESC, create DESC');
        	
        } elseif ($mode == 'close') {
            $dataProvider->sort->defaultOrder = ['close' => SORT_DESC];
        	$dataProvider->query->joinWith([
        		'closeUser' => function ($q) {
        			$q->from(['u' => User::tableName()]);
    			}
        	])->andWhere([
        		'and',	
        		['task.status' => true],
        		['is not', 'device_id', null]
        	]);
        }
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        	'mode' => $mode,	
        ]);
    }
   
    public function actionUpdate($id)
    {
    	$request = \Yii::$app->request;
    	
    	if ($request->isAjax){
    		$task = $this->findModel($id);
    		$task->scenario = DeviceTask::SCENARIO_UPDATE;
    		
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
    				$task->status = true;
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
