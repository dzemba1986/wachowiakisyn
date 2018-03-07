<?php

namespace backend\controllers;

use backend\models\Virtual;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class VirtualController extends DeviceController
{	
    function actionValidation() {
        
        $request = Yii::$app->request;
        $virtual = new Virtual();
        
        if ($virtual->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($virtual);
        };
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
