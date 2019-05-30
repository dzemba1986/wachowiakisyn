<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Camera;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class CameraController extends DeviceController {
    
    static public $model = Camera::class;
    
    public function behaviors() {
        
        return ArrayHelper::merge(
            parent::behaviors(),
            ['access' => [
                'class' => AccessControl::className(),
                'rules'	=> [
                    [
                        'allow' => true,
                        'actions' => ['get-change-mac-script', 'list'],
                        'roles' => ['@']
                    ]
                ]
            ]]
            );
    }
    
    public function actionList($q) {
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'alias' => '']];
        
        if (!is_null($q)) {
            
            $query = new Query();
            $query->select(['d.id', 'd.alias'])
            ->from('device d')
            ->where([
                'and',
                ['like', "replace(lower(d.alias), '_', ' ')", str_replace('_', ' ', strtolower($q)) . '%', false],
                ['<>', 'address_id', 1],
                ['is not', 'status', null],
                ['d.type_id' => Camera::TYPE]
            ])->orderBy('d.alias');
            
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        
        return $out;
    }
    
    public function actionGetChangeMacScript($id, $dId) {
        
        $camera = $this->findModel($id);
        $dCamera = $this->findModel($dId);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $camera->configChangeMac($dCamera->mac);
    }    
}
