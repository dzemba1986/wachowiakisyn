<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Swith;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class SwithController extends DeviceController
{	
    public function behaviors() {
        
        return ArrayHelper::merge(
            parent::behaviors(),
            ['access' => [
                'class' => AccessControl::className(),
                'rules'	=> [
                    [
                        'allow' => true,
                        'actions' => ['get-ip-link'],
                        'roles' => ['@']
                    ]
                ]
            ]]
            );
    }
    
    function actionGetIpLink($id) {
        
        return Html::a('ssh', "ssh://{$this->findModel($id)->ips[0]->ip}:22222");
    }
    
    protected static function getModelClassName() {
        
        return Swith::className();
    }
    
    protected static function getModel() {
        
        return new Swith();
    }
}
