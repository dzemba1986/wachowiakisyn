<?php

namespace backend\controllers;

use backend\models\Device;
use backend\models\DeviceSearch;
use Yii;
use yii\base\Exception;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StoreController extends Controller {
    
    public function behaviors() {
        
        return [
            [
                'class' => AjaxFilter::className(),
                'only' => ['add']
            ],
        ];
    }
    
    public function actionIndex() {

        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        $dataProvider->query->andWhere(['address_id' => 1]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
    
    public function actionAdd($typeId) {
        
    	$device = Device::create($typeId);   	
    	$device->scenario = Device::SCENARIO_CREATE;
           
        $request = Yii::$app->request;
        
        if ($device->load($request->post())) {
        	
        	$device->status = null;
        	$device->address_id = 1;
        	
            try {
            	if(!$device->save()) throw new Exception('Problem z zapisem urządzenia');
            	
            	return 1;
            } catch (Exception $e) {
            	var_dump($device->errors);
            	var_dump($e->getMessage());
            	exit();
            }    
                
        } else {
            switch (get_class($device)){
                case 'backend\models\Router':
                    echo $this->renderAjax('add_router', [
                    'device' => $device,
                    ]);
                    break;
                case 'backend\models\Swith':
                    
                    echo $this->renderAjax('add_switch', [
                    'device' => $device,
                    ]);
                    break;
                case 'backend\models\GatewayVoip':
                    
                    echo $this->renderAjax('add_gateway_voip', [
                    'device' => $device,
                    ]);
                    break;
                case 'backend\models\Camera':
                    
                    echo $this->renderAjax('add_camera', [
                    'device' => $device,
                    ]);
                    break;
                case 'backend\models\Server':
                    
                    echo $this->renderAjax('add_server', [
                    'device' => $device,
                    ]);
                    break;
                case 'backend\models\Virtual':
                    
                    echo $this->renderAjax('add_virtual', [
                    'device' => $device,
                    ]);
                    break;
                case 'backend\models\MediaConverter':
                    
                    echo $this->renderAjax('add_media_converter', [
                    'device' => $device,
                    ]);
                    break;
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
