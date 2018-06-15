<?php

namespace backend\controllers;

use backend\models\GatewayVoip;
use backend\models\Tree;
use Yii;
use yii\base\Exception;
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
    
    function actionAddToStore($id, $port) {
        
        $request = Yii::$app->request;
        $gv = $this->findModel($id);
        
        if($request->isPost){
            
            $link = Tree::findOne(['device' => $id, 'port' => $port]);
            $count = Tree::find()->where(['device' => $id])->count();
            
            try {
                if (!$gv->isParent()) {
                    $transaction = Yii::$app->getDb()->beginTransaction();
                    
                    if ($count == 1) {    //ostatnia kopia
                        $gv->address_id = 1;
                        $gv->status = null;
                        $gv->name = null;
                        $gv->proper_name = null;
                        $gv->monitoring = false;
                        $gv->geolocation = null;
                        
                        foreach ($gv->ips as $ip)
                            if (!$ip->delete()) throw new Exception('Błąd usuwania IP');
                            
                            if (!$link->delete()) throw new Exception('Błąd usuwania agregacji');
                            if (!$gv->save()) throw new Exception('Błąd zapisu urządzenia');
                    } else
                        if(!$link->delete()) throw new Exception('Błąd usuwania agregacji');
                        
                } else return 'Urządzenie jest rodzicem';
                
                $transaction->commit();
                return 1;
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                var_dump($gv->errors);
                var_dump($t->getMessage());
                exit();
            }
        } else
            return $this->renderAjax('@app/views/device/add_to_store', [
                'device' => $gv,
            ]);
    }
    
    public function actionReplace($id) {
        
        $request = Yii::$app->request;
        
        if($request->isPost) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            $sourceGv = $this->findModel($id);
            $sourceGv->scenario = GatewayVoip::SCENARIO_REPLACE;
            $destinationGv = $this->findModel($request->post('destinationDeviceId'));
            $destinationGv->scenario = GatewayVoip::SCENARIO_REPLACE;
            
            try {
                $link = Tree::findOne(['device' => $id]);
                if (!is_object($link)) throw new Exception('Nie znalazł linku');
                $link->device = $destinationGv->id;
                if (!$link->save()) throw new Exception('Błąd zapisu linku');
                
                foreach ($sourceGv->ips as $ip) {
                    $ip->device_id = $destinationGv->id;
                    if (!$ip->save()) throw new Exception('Błąd zapisu ip');
                }
                
                $destinationGv->address_id = $sourceGv->address_id;
                $destinationGv->status = $sourceGv->status;
                $destinationGv->name = $sourceGv->name;
                $destinationGv->proper_name = $sourceGv->proper_name;
                $destinationGv->monitoring = $sourceGv->monitoring;
                $destinationGv->geolocation = $sourceGv->geolocation;
                
                $sourceGv->address_id = 1;
                $sourceGv->status = null;
                $sourceGv->name = null;
                $sourceGv->proper_name = null;
                $sourceGv->geolocation = null;
                $sourceGv->monitoring = null;
                
                if (!($sourceGv->save() && $destinationGv->save())) throw new Exception('Błąd zapisu urządzenia');
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                echo $t->getMessage();
                var_dump($destinationGv->errors);
                var_dump($sourceGv->errors);
                exit();
            }
            
            $transaction->commit();
            return 1;
        } else {
            return $this->renderAjax('replace', [
                'id' => $id
            ]);
        }
    }
    
    public function actionGetChangeMacScript($id, $destId) {
        
        $request = Yii::$app->request;
        $gv = $this->findModel($id);
        $destGv = $this->findModel($destId);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $gv->configurationChangeMac($destGv->mac);
    }
    
    protected static function getModel() {
        
        return new GatewayVoip();
    }
}
