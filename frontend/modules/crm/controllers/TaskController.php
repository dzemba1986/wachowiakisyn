<?php

namespace frontend\modules\crm\controllers;

use common\models\crm\Task;
use common\models\crm\TaskSearch;
use Exception;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TaskController extends Controller {
    
    public function behaviors() {
        
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules'	=> [
                    [
                        'allow' => true,
                        'actions' => [
                            'calendar', 'calendar-ajax', 'index', 'update', 'view', 'close'
                        ],
                        'roles' => ['@']
                    ]
                ]
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['calendar-ajax', 'view', 'update', 'close']
            ],
        ];
    }
    
    public function actionCalendar() {
    
        $session = \Yii::$app->session;
        $session->remove('connectionId');
        
        return $this->render('calendar');
    }
    
    public function actionCalendarAjax($connectionId = null) {
        
        $session = \Yii::$app->session;
        
        if ($connectionId) $session->set('connectionId', $connectionId);
        
        return $this->renderAjax('calendar');
    }

    public function actionIndex() {
        
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());
        
        $dataProvider->query->select([
            'task.id', 'task.create_at', 'task.create_by', 'task.type_id', 'task.status', 'address_id', 'category_id', 'task.desc', 
            'close_by', 'task.close_at', 'fulfit', 'start_at', 'end_at', 'programme', 
            'address_string' => new Expression("ulica || ' ' || dom || dom_szczegol || '/' || lokal || lokal_szczegol")
        ])->andWhere(['not in', 'task.type_id', [1,8,9]])->orderBy(['address_id' => SORT_DESC, 'create_at' => SORT_DESC]);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionView($id) {
        
        $task = $this->findModel($id);
        
        return $this->renderAjax('view', [
            'task' => $task,
        ]);
    }
    
    public function actionUpdate($id) {
        
        $request = \Yii::$app->request;
        
        $task = $this->findModel($id);
        $task->scenario = Task::SCENARIO_UPDATE;
        
        if ($task->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            try {
                if (!$task->save()) throw new Exception('Problem z zapisem zadania');
            } catch (Exception $e) {
                return [0, $e->getMessage()];
            }
            
            return [1, 'Uaktualniono zadanie'];
            
        } else {
            return $this->renderAjax('update', [
                'task' => $task,
            ]);
        }
    }
    
    public function actionClose($id) {
        
        $request = \Yii::$app->request;
        
        $task = $this->findModel($id);
        $task->scenario = Task::SCENARIO_CLOSE;
        
        if ($task->load($request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $transaction = Yii::$app->db->beginTransaction();
            
            try {
                $task->trigger(get_class($task)::EVENT_CLOSE_TASK);
                if (!$task->save()) throw new Exception('Problem z zamknięciem zadania');
            } catch (Exception $e) {
                $transaction->rollBack();
                return [0, $e->getMessage()];
            }
            
            $transaction->commit();
            return [1, 'Zamknięto zadanie'];
        } else {
            return $this->renderAjax('close', [
                'task' => $task
            ]);
        }
    }
    
    protected function findModel($id) {
        
        if (($model = Task::findOne($id)) !== null) return $model;
        else throw new NotFoundHttpException('The requested page does not exist.');
    }
}
