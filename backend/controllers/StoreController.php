<?php

namespace backend\controllers;

use Yii;
use backend\models\Device;
use backend\models\DeviceSearch;
use yii\base\Exception;
use backend\models\DeviceFactory;
use yii\web\Controller;

class StoreController extends Controller
{ 
    public function actionIndex()
    {
        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        $dataProvider->query->andWhere(['address' => NULL]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    
    public function actionAdd($type)
    {
    	$modelDevice = DeviceFactory::create($type);   	
    	$modelDevice->scenario = Device::SCENARIO_CREATE;
           
        $request = Yii::$app->request;
        
    	if ($request->isAjax){
            if ($modelDevice->load($request->post())) {
            	if($modelDevice->validate()){
	                try {
	                	if(!$modelDevice->save())
	                		throw new Exception('Problem z zapisem urządzenia');
	                	return 1;
	                } catch (Exception $e) {
	                	var_dump($modelDevice->errors);
	                	var_dump($e->getMessage());
	                	exit();
	                }    
	                
            	} else {
            		var_dump($modelDevice->errors);
	               	exit();
            	}
            } else {
                return $this->renderAjax('add', [
                    'modelDevice' => $modelDevice,
                ]);
            }
        } 
    }
    
    public function actionUpdate($id)
    {
        $modelDevice = $this->findModel($id);
        $modelDevice->scenario = Device::SCENARIO_UPDATE;
        
        $request = Yii::$app->request;
        
        if($request->isAjax){
        	if($modelDevice->load($request->post())){
            	if($modelDevice->validate()){
            		try {
            			if(!$modelDevice->save())
            				throw new Exception('Problem z zapisem urządzenia');
            			return 1;
            		} catch (Exception $e) {
            			var_dump($modelDevice->errors);
            			exit();
            		}
            	}  
            } else {
                return $this->renderAjax('update', [
                    'modelDevice' => $modelDevice
                ]);
            }
        }
    }

    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();
    	$request = Yii::$app->request;
    	
    	if($request->isAjax){
    		
    		if($request->post('yes')){
    			$this->findModel($id)->delete();
    			return 1;
    		} else{
    			
    			return $this->renderAjax('delete', [
    			]);
    		}   
    	}
    }
    
    protected function findModel($id)
    {
    	if (($model = Device::findOne($id)) !== null) {
    		return $model;
    	} else {
    		throw new NotFoundHttpException('The requested page does not exist.');
    	}
    }
}
