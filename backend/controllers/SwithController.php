<?php

namespace backend\controllers;

use backend\models\Swith;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SwithController extends Controller
{	
    function actionValidation($id = null) {
        
        $request = Yii::$app->request;
        $switch = is_null($id) ? new Swith() : Swith::findOne($id);
        
        if ($switch->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($switch);
        };
    }
    
    protected function findModel($id)
    {
        if (($model = Swith::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
