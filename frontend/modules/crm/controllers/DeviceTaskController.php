<?php

namespace frontend\modules\crm\controllers;

use common\models\crm\DeviceTask;
use common\models\crm\DeviceTaskSearch;
use frontend\modules\crm\models\forms\CreateMonitoringTask;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DeviceTaskController extends Controller {
    
    public function actionCreate() {
        
        $model = new CreateMonitoringTask();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->create()) {
                return 1;
            } else
                return 0;
        }
        
        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }
    
    public function actionGetCountOpenTask() {
        
        $session = Yii::$app->session;
        
        if (!is_null($session->get('openTask'))) $out = $session->get('openTask');
        else {
            $count = DeviceTask::find()->where(['status' => 0])->count();
            $session->set('openTask', $count);
            $out = $count;
        }
        
        return $out;
    }
    
    public function actionIndex() {
        
        $searchModel = new DeviceTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
    	$dataProvider->query->select([
    	   'task.id', 'task.create_at', 'task.type_id', 'task.status', 'category_id', 'label_id', 'task.desc', 'device_id', 'close_by', 'task.close_at', 'fulfit',
    	    'comments_count' => new Expression('COUNT(task_id)')
    	])->joinWith('comments')->groupBy('task_id, task.id, name');
    	
    	return $this->render('index', [
    	    'searchModel' => $searchModel,
    	    'dataProvider' => $dataProvider,
    	]);
        	
    }
   
    public function actionUpdate($id) {
        
    	$request = \Yii::$app->request;
    	
		$task = $this->findModel($id);
// 		var_dump($task->day); exit();
		$task->scenario = DeviceTask::SCENARIO_UPDATE;
		
		if ($task->load($request->post())) {
			try {
				if (!$task->save()) throw new Exception('Problem z zapisem');
			} catch (Exception $e) {
				print_r($task);
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
    
    public function actionClose($id) {
    	
    	$request = \Yii::$app->request;
    	
		$task = $this->findModel($id);
		$task->scenario = DeviceTask::SCENARIO_CLOSE;
		
		if ($task->load($request->post())){
			try {
				$task->trigger(DeviceTask::EVENT_CLOSE_TASK);
				if (!$task->save()) throw new Exception('Problem z zamknięciem zadania');
			} catch (Exception $e) {
				print_r($task->errors); exit();
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
