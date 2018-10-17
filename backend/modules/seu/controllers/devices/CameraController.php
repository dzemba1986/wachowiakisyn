<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\Camera;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class CameraController extends DeviceController
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
    
    public function actionGetChangeMacScript($id, $dId) {
        
        $camera = $this->findModel($id);
        $dCamera = $this->findModel($dId);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $camera->configChangeMac($dCamera->mac);
    }
    
    protected static function getModel() {
        
        return new Camera();
    }
}
