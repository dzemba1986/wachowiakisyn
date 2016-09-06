<?php

namespace backend\controllers;

use Yii;
use yii\widgets\ActiveForm;
use backend\models\GatewayVoip;

class GatewayVoipController extends DeviceController
{	
	public function actionValidation($id = null){
		 
		$modelDevice = is_null($id) ? new GatewayVoip() : $this->findModel($id);
		
		$request = Yii::$app->request;
		
		if ($request->isAjax && $modelDevice->load($request->post())) {
			
//  				var_dump($modelDevice); exit();
	           	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	               	return ActiveForm::validate($modelDevice, 'mac');

              	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($modelDevice, 'serial');

		}
	}
    
    protected function findModel($id)
    {
        if (($model = GatewayVoip::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
