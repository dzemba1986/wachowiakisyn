<?php

namespace backend\controllers;

use backend\models\GatewayVoip;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class GatewayVoipController extends DeviceController
{	
    function actionValidation($id = null) {
        
        $request = Yii::$app->request;
        $voip = is_null($id) ? new GatewayVoip() : GatewayVoip::findOne($id);
        
        if ($voip->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($voip);
        };
    }
    
    protected function findModel($id) {
        
        if (($model = GatewayVoip::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
