<?php

namespace backend\controllers;

use backend\models\Server;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ServerController extends DeviceController
{	
    function actionValidation() {
        
        $request = Yii::$app->request;
        $server = new Server();
        
        if ($server->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($server);
        };
    }
    
    protected function findModel($id)
    {
        if (($model = Server::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
