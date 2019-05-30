<?php

namespace frontend\modules\crm\controllers;

use common\models\crm\Blockage;
use yii\base\Exception;
use yii\db\Expression;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BlockageController extends Controller {
    
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
    
    public function actionGet($start = null, $end = null, $_ = null) {
        
    	$tasks = Blockage::find()->select([
    	    'task.id', 'start' => 'start_at', 'end' =>'end_at', 'description' => 'desc', 'type' => 'type_id', 
    	    'category' => new Expression("CASE WHEN category_id = 1 THEN 'Blokada' ELSE 'Rezerwacja' END"),
    	    'calendar' => new Expression("CASE WHEN receive_by = 1 THEN 'Serwis' ELSE 'Szczurek' END"),
    	    'title' => new Expression("CASE WHEN category_id = 1 THEN 'Blokada' ELSE 'Rezerwacja' END"),
	    ])->where(['and', ['between', 'start_at', $start, $end], ['status' => [0,2]]])->orderBy('start_at')->asArray()->all();
    	
	    return $tasks;
    }
    
    public function actionCreate($timestamp) {
    
    	$request = \Yii::$app->request;
		$task = \Yii::createObject([
		    'class' => Blockage::class, 
		    'scenario' => Blockage::SCENARIO_CREATE, 
		]);
		
		if ($request->isPost) {
        	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		    try {
        	    if ($task->load($request->post())) {
        	        if (!$task->save()) throw new Exception('Problem z zapisem blokady/rezerwacji');
        	    }
		    } catch (Exception $e) {
                return [0, [$e->getMessage()]];
		    }
		    return [1, 'Blokada/rezerwacja dodana'];
		} else {
		    $task->day = date('Y-m-d', $timestamp);
		    $task->start_time = date('H:i', $timestamp);
		    $task->end_time = date('H:i', $timestamp + 3600);
		    
		    return $this->renderAjax('create', [
		        'task' => $task,
		    ]);
		}
    }
    
    public function actionUpdate($id) {
        
    	$request = \Yii::$app->request;
    	
		$task = $this->findModel($id);
		$task->scenario = Blockage::SCENARIO_UPDATE;
    		
		if ($task->load($request->post())) {
        	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			try {
				if (!$task->save()) throw new Exception('Problem z zapisem blokady/rezerwacji');
			} catch (Exception $e) {
				return [0, $e->getMessage()];
			}

			return [1, 'Blokadę/rezerwację uaktualniono'];
					
		} else {
			return $this->renderAjax('update', [
				'task' => $task,
			]);
		}
    }
    
    public function actionClose($id) {
    	
    	$request = \Yii::$app->request;
    	
		$task = $this->findModel($id);
		$task->scenario = Blockage::SCENARIO_CLOSE;
        
        if ($request->isPost) {
        	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		try {
            	$task->trigger(Blockage::EVENT_CLOSE_TASK);	
    			if (!$task->save()) throw new Exception('Problem z zapisem blokady');
    		} catch (Exception $e) {
    			return [0, $e->getMessage()];
    		}
    		
    		return [1, 'Blokadę zamknięto'];
    	} else {
    		return $this->renderAjax('close');
    	}
    }

    protected function findModel($id)
    {
        if (($model = Blockage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
