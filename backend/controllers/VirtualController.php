<?php

namespace backend\controllers;

use Yii;
use yii\widgets\ActiveForm;
use backend\models\Virtual;

class VirtualController extends DeviceController
{	
	public function actionValidation($id = null){
		 
		$modelDevice = is_null($id) ? new Virtual() : $this->findModel($id);
		
		$request = Yii::$app->request;
		
		if ($request->isAjax && $modelDevice->load($request->post())) {
			
//  				var_dump($modelDevice); exit();
	           	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	               	return ActiveForm::validate($modelDevice, 'mac');
		}
	}
    
    protected function findModel($id)
    {
        if (($model = Virtual::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
