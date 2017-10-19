<?php

namespace backend\modules\task\controllers;

use Yii;
use backend\models\Task;
use backend\models\TaskSearch;
use backend\models\Connection;
use backend\models\Address;
use yii\web\Controller;
use common\models\User;
use backend\models\Installation;
use yii\base\Exception;

class TaskController extends Controller
{
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * Lists all Modyfication models.
     * @return mixed
     */
    public function actionIndex($mode = 'todo')
    {
        $searchModel = new TaskSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        if ($mode == 'todo') {
        	$dataProvider->query->andWhere([
        		'close_date' => null,
        	])->andWhere(['or', ['task.status' => true], ['is', 'task.status', null]]);
        	
        	$dataProvider->query->orderBy('start_date, start_time');
        	
        } elseif ($mode == 'close') {
        	$dataProvider->query->joinWith([
        		'modelCloseUser' => function ($q) {
        			$q->from(['u' => User::tableName()]);
    			}
        	])->andWhere([
        		'is not', 'close_date', null,
        		//'is not', 'close_user', null,
        	]);
        	
        	$dataProvider->query->orderBy('close_date');
        }
        
        //$dataProvider->query->orderBy('start_date, start_time');

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        	'mode' => $mode,	
        ]);
    }

    public function actionTaskCalendar($start = NULL, $end = NULL, $_ = NULL){
    	 
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        
            	
    	$tasks = Task::find()->where(['between', 'start_date', $start, $end])->andWhere(['or', ['task.status' => true], ['is', 'task.status', null]])->orderBy('start_date')->asArray()->all();
        
//         var_dump($tasks); exit();
    	
        $tasks = array_map(function($task) {
            return array(
                'id' => $task['id'],
                'start' => $task['start_date'].' '.$task['start_time'],
                'end' => $task['end_date'].' '.$task['end_time'],
                'title' => Address::findOne($task['address'])->toString(true),
            	'description' => $task['description']	
            );
        }, $tasks);
        
        return $tasks;
    }
    
    
    /**
     * Displays a single Modyfication model.
     * @param string $id
     * @return mixed
     */
    public function actionViewCalendar($conId = null){
    	
        if(Yii::$app->request->isAjax){        

            return $this->renderAjax('calendar', [
                'conId' => $conId,	
            ]);
        }
    }
    
    public function actionDragEvents(){
    	
        if(Yii::$app->request->isAjax){
            
            $dataAjax = Yii::$app->request->post();
            $modelTask = $this->findModel(explode(":", $data['id']));
            $modelTask->start = explode(":", $data['start']);
            $modelTask->end = explode(":", $data['end']);
           
            $modelTask->save();
//            if ($modelTask->load(Yii::$app->request->post())) {
//
//            }
        }
    	
    }
    
    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($timestamp, $conId = null) {
    	
    	$modelTask = new Task();
    	$modelAddress = new Address();
    	$modelConnection = NULL;
    	
    	$modelTask->scenario = Task::SCENARIO_CREATE;
    	
    	if (!is_null($conId)){ //jeżeli tworzymy zadanie z LP
    	
    		$modelConnection = Connection::findOne($conId);
    	}
    	
    	if(Yii::$app->request->isAjax){
    		
	    	if ($modelTask->load(Yii::$app->request->post())){	
	    		
	    		$transaction = Yii::$app->getDb()->beginTransaction();  
	    		
	    		if (is_object($modelConnection)){ //jeżeli zadanie tworzymy z LP
	    			
	    			$modelTask->address = $modelConnection->address;
	    			$modelTask->nocontract = $modelConnection->nocontract;
	    			
	    		} else { //zadanie poza LP
	    			if ($modelAddress->load(Yii::$app->request->post())){
	    				
	    				try {
	    					if (!$modelAddress->save())
	    						throw new Exception('Problem z zapisem address');
	    				} catch (Exception $e) {
	    					$transaction->rollBack();
	    					var_dump($modelAddress->errors);
	    					exit();
	    				}
	    			}
	    			
	    			$modelTask->address = $modelAddress->id;
	    		}
	    		
	    		
	    		$modelTask->add_user = Yii::$app->user->identity->id;
	    		$modelTask->start_time = $modelTask->start_time.':00';
	    		$modelTask->end_time = $modelTask->end_time.':00';
	    		$modelTask->editable = true;
	    		$modelTask->status = null;
	    		
				try {
					if (!$modelTask->save())
						throw new Exception('Problem z zapisem task');
					if (is_object($modelConnection)){
						$modelConnection->task = $modelTask->id;
						if (!$modelConnection->save())
							throw new Exception('Problem z zapisem connection');
					}
					
					$transaction->commit();
					
				} catch (Exception $e) {
					$transaction->rollBack();
					var_dump($modelTask);
					var_dump($modelConnection);
					exit();
				}
				
				return 1;
	    	} else {
	    		
	    		$dateTime = new \DateTime();
	    		$dateTime->setTimestamp($timestamp / 1000);
	    		
	    		$modelTask->start_date = $dateTime->format('Y-m-d');
	    		$modelTask->end_date = $dateTime->format('Y-m-d');
	    		$modelTask->start_time = $dateTime->format('H:i');
	    		$modelTask->end_time = $dateTime->add(new \DateInterval('PT1H'))->format('H:i');
	    		
	    		
	    		if (is_object($modelConnection)){ //jeżeli tworzymy zadanie z LP

	    			$modelTask->type = $modelConnection->type;
	    			$modelTask->phone = $modelConnection->phone;
	    		}
	    			
	    		return $this->renderAjax('create', [
	    				'modelTask' => $modelTask,
	    				'modelConnection' => $modelConnection,
	    				'modelAddress' => $modelAddress,
	    				'dateTime' => $dateTime,
	    		]);
	    	}
    	}
    	
    	
    	
    	
    	
//         $modelTask = new Task();
        
//         if ($modelTask->load(Yii::$app->request->post())){
        
//             if($connectionId <> NULL){

//                 $modelConnection = Connection::findOne($connectionId);
                
//                 $modelTask->address = $modelConnection->address;
//     			$modelTask->connection = $modelConnection->id;
//     			$modelTask->title = $modelConnection->modelAddress->fullAddress;
                
                
//             }
//             else {
                
//                 $modelAddress = new Address();
    			
//     			if($modelAddress->load(Yii::$app->request->post())){
    				
//     				$address = Address::checkAddress($modelAddress);
    				
//     				if ($address){
//     					$modelTask->address = $address->id;
//     					$modelTask->con_id = null;
//     					$modelTask->title = $address->fullAddress;
//     					//var_dump($address);
//     					//exit();
//     				}
//     				else{
//     					var_dump($address);
//     					exit();
//     				}
//     			}
//             }

//             $modelTask->start = $modelTask->dateFrom.' '.$modelTask->timeFrom.':00';
//     		$modelTask->end = $modelTask->dateTo.' '.$modelTask->timeTo.':00';
//     		$modelTask->add_user = Yii::$app->user->identity->id;
    		
//     		if($modelTask->save()){
//                 $modelConnection->task = $modelTask->id;
//                 $modelConnection->save();
//     			echo 1;
//             }    
//     		else{
//     			echo 0;
//     		}
//         }
//         else {
            
//             if(Yii::$app->request->isAjax){
                
//                 $dateTime = new \DateTime();
//                 $dateTime->setTimestamp($timestamp / 1000);

//                 return $this->renderAjax('create', [
//                     'modelTask' => $modelTask,
//                     'connectionId' => $connectionId,
//                     'dateTime' => $dateTime,
//                 ]);
//             }
//         }
    }

    /**
     * Updates an existing Modyfication model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $modelTask = $this->findModel($id);
        $modelTask->scenario = Task::SCENARIO_UPDATE;

        if ($modelTask->load(Yii::$app->request->post())){
        	
        	$modelTask->start_time = $modelTask->start_time.':00';
    		$modelTask->end_time = $modelTask->end_time.':00';
    		$modelTask->all_day == 1 ? $modelTask->all_day = true : $modelTask->all_day = false;
        	
    		//var_dump($modelTask->validate(['create'])); exit();
        	if ($modelTask->save()) 
            	return 1;
        	else 
        		return 0;
        	    
        } else {            
            
            return $this->renderAjax('update', [
                'modelTask' => $modelTask,
            ]);
        }
    }

    public function actionClose($id)
    {
        $modelTask = $this->findModel($id);
        $modelTask->scenario = Task::SCENARIO_CLOSE;
        
        $modelConnection = Connection::find()->where(['task' => $modelTask->id])->one();
        
        if(Yii::$app->request->isAjax){
        
	        if ($modelTask->load(Yii::$app->request->post())){
	        	
	        	$transaction = Yii::$app->db->beginTransaction();
	        	
		        if (is_object($modelConnection)){ //zadanie powiązane z LP
		        	
		        	if ($modelTask->status == true) { //zadanie wykonano
		        		
			        	$modelInstallation = null;
			        	//przeszukaj wszystkie instalacje typu podłączenia
			        	foreach ($modelConnection->modelInstallationsByType as $installation){
			        		
			        		if (!is_null($installation->wire_date) && is_null($installation->socket_date) && is_null($installation->socket_user)){
			        			// jeżeli znalazł jakąś instalację z kablem, bez gniazda i bez socket usera to podstaw
			        			$modelInstallation = $installation;
			        			break;
			        		} 
			        	}
			        	
			        	if (is_object($modelInstallation)) {
			        		
			        		$modelInstallation->scenario = Installation::SCENARIO_SOCKET;
			        		
			        		try {
			        			$modelInstallation->socket_date = date('Y-m-d');
			        			$modelInstallation->socket_user = implode(",", $modelTask->installer);

			        			if (!$modelInstallation->save()) 
			        				throw new Exception;
			        		} catch (Exception $e) {
			        			$transaction->rollBack();
			        			throw $e;
			        		}
			        	} else {
								return 'Nie znaleziono instalacji z kablem a bez gniazda';			        			
			        	}
			        	
			        	if ($modelConnection->load(Yii::$app->request->post())){ //wpisano mac
			        		 
			        		try {
			        			if (!$modelConnection->save()) 
			        				throw new Exception('Problem z zapisem mac');
			        		} catch (Exception $e) {
			        			$transaction->rollBack();
			        			var_dump($modelConnection->errors);
			        			exit();
			        		}
			        	}
		        	}
		        	 try {
		        	 	$modelConnection->task = null;
		        	 	$modelConnection->info = $modelConnection->info.' '.\Yii::$app->request->post('desc');
		        	 	if (!$modelConnection->save()) 
		        	 		throw new Exception('Problem z zapisem conn');
		        	 } catch (Exception $e) {
		        	 	$transaction->rollBack();
		        	 	throw $e;
		        	 }
		        	 
		        	 $modelTask->description = $modelTask->description.' '.\Yii::$app->request->post('desc');
		        }
		        
		        try {
		        	$modelTask->installer = implode(",", $modelTask->installer);
		        	$modelTask->close_user = Yii::$app->user->identity->id;
		        	$modelTask->editable = false;
		        	$modelTask->validate();
		        	
		        	if (!$modelTask->save()) 
		        		throw new Exception('Problem z zapisem montażu');
		        	$transaction->commit();
		        	return 1;
		        } catch (Exception $e) {
		        	$transaction->rollBack();
		        	var_dump($modelTask);
		        	exit();
		        }
	        } else {
	        	
	            return $this->renderAjax('close', [
	                'modelTask' => $modelTask,
	            	'modelConnection' => $modelConnection,	
	            ]);
	        }
        }
    }
    
    /**
     * Deletes an existing Modyfication model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['view-calendar']);
    }

    /**
     * Finds the Modyfication model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Modyfication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
