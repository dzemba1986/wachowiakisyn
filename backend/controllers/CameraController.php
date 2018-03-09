<?php

namespace backend\controllers;

use backend\models\Camera;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class CameraController extends Controller
{	
    function actionValidation($id = null) {
        
        $request = Yii::$app->request;
        $camera = is_null($id) ? new Camera() : Camera::findOne($id);
        
        if ($camera->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($camera);
        };
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
