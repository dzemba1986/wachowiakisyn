<?php

namespace backend\controllers;

use backend\models\MediaConverter;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class MediaConverterController extends Controller
{	
    function actionValidation($id = null) {
        
        $request = Yii::$app->request;
        $host = is_null($id) ? new MediaConverter() : MediaConverter::findOne($id);
        
        if ($host->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($host);
        };
    }
	
    protected function findModel($id)
    {
        if (($model = MediaConverter::findOne($id)) !== null) {
        	return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
