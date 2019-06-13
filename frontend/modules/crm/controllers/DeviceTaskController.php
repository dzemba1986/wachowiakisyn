<?php

namespace frontend\modules\crm\controllers;

use common\models\crm\DeviceTask;
use common\models\crm\DeviceTaskSearch;
use frontend\modules\crm\models\forms\CameraTask;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class DeviceTaskController extends TaskController {
    
    public function behaviors() {
        
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules'	=> [
                        [
                            'allow' => true,
                            'actions' => [
                                'get-count-open-task', 'create-camera-task', 'check-double'
                            ],
                            'roles' => ['@']
                        ]
                    ]
                ],
            ]
        );
    }
    
    public function actionCreateCameraTask() {
        
        $model = new CameraTask();
        if ($model->load(Yii::$app->request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            
            if ($model->create()) {
                return [1, 'Dodano zgłoszenie'];
            } else
                return [0, 'Błąd dodania zgłoszenia'];
        }
        
        return $this->renderAjax('create_camera_task', [
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

    public function actionCheckDouble($deviceId) {
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return DeviceTask::find()->where(['device_id' => $deviceId, 'status' => [0,2]])->count();
    }
    
    public function actionIndex() {
        
        $searchModel = new DeviceTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
    	$dataProvider->query->select([
    	   'task.id', 'task.create_at', 'task.type_id', 'task.status', 'category_id', 'task.desc', 'device_id', 'task.close_by', 'task.close_at', 'fulfit',
    	    'comments_count' => new Expression('COUNT(task_id)')
    	])->joinWith(['comments'])->groupBy('task_id, task.id, name');

    	return $this->render('index', [
    	    'searchModel' => $searchModel,
    	    'dataProvider' => $dataProvider,
    	]);
        	
    }
   
//     public function actionClose($id) {
    	
//     	$request = \Yii::$app->request;
    	
// 		$task = $this->findModel($id);
// 		$task->scenario = DeviceTask::SCENARIO_CLOSE;
		
// 		if ($task->load($request->post())){
// 			try {
// 				$task->trigger(DeviceTask::EVENT_CLOSE_TASK);
// 				if (!$task->save()) throw new Exception('Problem z zamknięciem zadania');
// 			} catch (Exception $e) {
// 				print_r($task->errors); exit();
// 				echo $e->getMessage();
// 				return 0;
// 			}
			
// 			return 1;
// 		} else {
// 			return $this->renderAjax('close', [
// 				'task' => $task
// 			]);
// 		}
//     }

    public function actionDelete($id) {
        
        $this->findModel($id)->delete();

        return $this->redirect(['view-calendar']);
    }
}
