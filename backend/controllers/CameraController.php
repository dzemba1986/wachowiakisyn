<?php

namespace backend\controllers;

use Yii;
use yii\widgets\ActiveForm;
use backend\models\Camera;
use yii\db\Query;
use yii\db\Expression;

class CameraController extends DeviceController
{	
	public function actionValidation($id = null){
		 
		$modelDevice = is_null($id) ? new Camera() : $this->findModel($id);
		
		$request = Yii::$app->request;
		
		if ($request->isAjax && $modelDevice->load($request->post())) {
			
              	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($modelDevice, 'serial');
		}
	}
	
	public function actionSearchForMonitoring($q = null) {
		
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$out = ['results' => ['id' => '', 'alias' => '']];
		
		if (!is_null($q)) {
				
			$query = new Query();
			$query->select(['d.id', 'd.alias'])
	    	->from('device d')
	    	->where(['and', ['like', 'd.alias', strtoupper($q) . '%', false], ['is not', 'address', null], ['is not', 'status', null], ['d.type' => Camera::TYPE]])
	    	->limit(50)->orderBy('d.alias');
	    	
    		$command = $query->createCommand();
    		$data = $command->queryAll();
    		$out['results'] = array_values($data);
		}
		
		return $out;
	}
    
    protected function findModel($id)
    {
        if (($model = Camera::findOne($id)) !== null) {
        	return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
