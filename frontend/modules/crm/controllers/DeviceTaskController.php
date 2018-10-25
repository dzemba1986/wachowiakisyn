<?php

namespace frontend\modules\crm\controllers;

use common\models\User;
use common\models\crm\DeviceTask;
use common\models\crm\DeviceTaskSearch;
use common\models\seu\devices\Camera;
use frontend\modules\crm\models\forms\CreateMonitoringTask;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DeviceTaskController extends Controller
{
    public function actionCreate() {
        
        $model = new CreateMonitoringTask();
        if ($model->load(Yii::$app->request->post())) {
            if ($task = $model->create()) {
                return 1;
            } else
                return 0;
        }
        
        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }
    
    public function actionIndex($mode = 'todo') {
        
        $searchModel = new DeviceTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        if ($mode == 'todo') {
        	$dataProvider->query->andWhere([
        		'and',
        		['close' => null],	
        		['or', ['task.status' => false], ['task.status' => null]],
        		['is not', 'device_id', null]
        	])->orderBy('status DESC, create DESC');
        	
        	$view = $this->render('grid_todo', [
        	    'searchModel' => $searchModel,
        	    'dataProvider' => $dataProvider,
        	]);
        	
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
        	
        	$view = $this->render('grid_close', [
        	    'searchModel' => $searchModel,
        	    'dataProvider' => $dataProvider,
        	]);
        	
        } elseif ($mode == 'monitoring') {
            $dataProvider->sort->defaultOrder = ['status' => SORT_DESC, 'create' => SORT_DESC];
            $dataProvider->query->andWhere([
                'and',
                ['is not', 'device_id', null],
                ['device_type' => Camera::TYPE],
                ['or', ['task.status' => false], ['task.status' => null]]
            ]);
            
            $view = $this->render('monitoring', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        
        return $view;
    }
   
    public function actionUpdate($id) {
        
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
    
    public function actionClose($id) {
    	
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

    public function actionDelete($id) {
        
        $this->findModel($id)->delete();

        return $this->redirect(['view-calendar']);
    }

    protected function findModel($id) {
        
        if (($model = DeviceTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
