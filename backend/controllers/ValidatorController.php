<?php

namespace backend\controllers;

use backend\models\Host;
use backend\models\forms\AddHostForm;
use Yii;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ValidatorController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => AjaxFilter::className(),
                'only' => ['add-host', 'host']
            ],
        ];
    }

    public function actionAddHost() {
        
        $request = Yii::$app->request;
        $model = new AddHostForm();
        
        if ($model->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model, 'mac');
        }
    }
    
    function actionHost() {
        
        $request = Yii::$app->request;
        $device = new Host();
        
        if ($device->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($device);
        };
    }
}
