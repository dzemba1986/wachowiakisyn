<?php

namespace backend\controllers;

use backend\models\Host;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class HostController extends DeviceController
{
    function actionGetMac($id) {
        
        $host = $this->findModel($id);
        return $host->mac;
    }
    
    function actionGetUrlCheckDhcp($id) {
        
        $host = $this->findModel($id);
        return "http://172.20.4.17:701/index.php?sourceid=3&filter=clientmac%3A%3D" . base_convert(preg_replace('/:/', '', $host->mac), 16, 10) . "&search=Search";
    }
    
    function actionValidation() {
        
        $request = Yii::$app->request;
        $host = new Host();
        
        if ($host->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($host);
        };
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
