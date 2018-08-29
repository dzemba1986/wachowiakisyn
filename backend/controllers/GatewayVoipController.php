<?php

namespace backend\controllers;

use backend\models\GatewayVoip;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class GatewayVoipController extends DeviceController
{
    public function behaviors() {
        
        return ArrayHelper::merge(
            parent::behaviors(),
            ['access' => [
                'class' => AccessControl::className(),
                'rules'	=> [
                    [
                        'allow' => true,
                        'actions' => ['get-change-mac-script'],
                        'roles' => ['@']
                    ]
                ]
            ]]
            );
    }
    
    public function actionGetChangeMacScript($id, $destId) {
        
        $gv = $this->findModel($id);
        $destGv = $this->findModel($destId);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $gv->configurationChangeMac($destGv->mac);
    }
    
    protected static function getModel() {
        
        return new GatewayVoip();
    }
}
