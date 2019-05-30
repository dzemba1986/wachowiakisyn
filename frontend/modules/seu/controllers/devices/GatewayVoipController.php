<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\GatewayVoip;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class GatewayVoipController extends DeviceController
{
    static public $model = GatewayVoip::class;
    
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
        
        $gv = $this->findModel($id);
        $dGv = $this->findModel($dId);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $gv->configChangeMac($dGv->mac);
    }
}
