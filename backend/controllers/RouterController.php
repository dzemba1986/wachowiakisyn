<?php

namespace backend\controllers;

use backend\models\Router;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class RouterController extends DeviceController
{	
    function actionValidation($id = null) {
        
        $request = Yii::$app->request;
        $router = is_null($id) ? new Router() : Router::findOne($id);
        
        if ($router->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($router);
        };
    }
    
    protected function findModel($id)
    {
        if (($model = Router::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
