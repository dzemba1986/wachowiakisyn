<?php

namespace backend\controllers;

use backend\models\Host;
use backend\models\forms\AddHostForm;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class HostController extends DeviceController
{
    function actionValidation($id = null) {
        
        $request = Yii::$app->request;
        $host = is_null($id) ? new Host() : Host::findOne($id);
        
        if ($host->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($host);
        };
    }
    
    function actionAddHostValidation() {
        
        $request = Yii::$app->request;
        $model = new AddHostForm();
        
        if ($model->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model, 'mac');
        }
    }
    
    protected function findModel($id)
    {
        if (($model = Host::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
