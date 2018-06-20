<?php

namespace backend\controllers;

use backend\models\Camera;
use backend\models\GatewayVoip;
use backend\models\Tree;
use Yii;
use yii\base\Exception;
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
    
    function actionDeleteFromTree($id, $port) {
        
        $request = Yii::$app->request;
        $camera = $this->findModel($id);
        
        if($request->isPost){
            
            $link = Tree::findOne(['device' => $id, 'port' => $port]);
            $count = Tree::find()->where(['device' => $id])->count();
            
            try {
                if (!$camera->isParent()) {
                    $transaction = Yii::$app->getDb()->beginTransaction();
                    
                    if ($count == 1) {    //ostatnia kopia
                        $camera->address_id = 1;
                        $camera->status = null;
                        $camera->name = null;
                        $camera->proper_name = null;
                        $camera->alias = null;
                        $camera->monitoring = false;
                        $camera->geolocation = null;
                        $camera->dhcp = null;
                        
                        
                        foreach ($camera->ips as $ip)
                            if (!$ip->delete()) throw new Exception('Błąd usuwania IP');
                            
                            if (!$link->delete()) throw new Exception('Błąd usuwania agregacji');
                            if (!$camera->save()) throw new Exception('Błąd zapisu urządzenia');
                    } else
                        if(!$link->delete()) throw new Exception('Błąd usuwania agregacji');
                        
                } else return 'Urządzenie jest rodzicem';
                
                $transaction->commit();
                return 1;
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                var_dump($camera->errors);
                var_dump($t->getMessage());
                exit();
            }
        } else
            return $this->renderAjax('@app/views/device/delete-from-tree', [
                'device' => $camera,
            ]);
    }
    
    public function actionReplace($id) {
        
        $request = Yii::$app->request;
        $sourceCamera = $this->findModel($id);
        
        if($request->isPost) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            $sourceCamera->scenario = GatewayVoip::SCENARIO_REPLACE;
            $destinationCamera = $this->findModel($request->post('destinationDeviceId'));
            $destinationCamera->scenario = GatewayVoip::SCENARIO_REPLACE;
            
            try {
                $link = Tree::findOne(['device' => $id]);
                if (!is_object($link)) throw new Exception('Nie znalazł linku');
                $link->device = $destinationCamera->id;
                if (!$link->save()) throw new Exception('Błąd zapisu linku');
                
                foreach ($sourceCamera->ips as $ip) {
                    $ip->device_id = $destinationCamera->id;
                    if (!$ip->save()) throw new Exception('Błąd zapisu ip');
                }
                
                if ($request->post('replaceMac', false)) {
                    $tempMac = $destinationCamera->mac;
                    $destinationCamera->mac = $sourceCamera->mac;
                    $sourceCamera->mac = $tempMac;
                }
                
                $destinationCamera->address_id = $sourceCamera->address_id;
                $destinationCamera->status = $sourceCamera->status;
                $destinationCamera->name = $sourceCamera->name;
                $destinationCamera->proper_name = $sourceCamera->proper_name;
                $destinationCamera->monitoring = $sourceCamera->monitoring;
                $destinationCamera->geolocation = $sourceCamera->geolocation;
                $destinationCamera->dhcp = $sourceCamera->dhcp;
                $destinationCamera->alias = $sourceCamera->alias;
                
                $sourceCamera->address_id = 1;
                $sourceCamera->status = null;
                $sourceCamera->name = null;
                $sourceCamera->proper_name = null;
                $sourceCamera->geolocation = null;
                $sourceCamera->monitoring = null;
                $sourceCamera->dhcp = false;
                $sourceCamera->alias = null;
                
                if (!($sourceCamera->save() && $destinationCamera->save())) throw new Exception('Błąd zapisu urządzenia');
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                echo $t->getMessage();
                var_dump($destinationCamera->errors);
                var_dump($sourceCamera->errors);
                exit();
            }
            
            $transaction->commit();
            return 1;
        } else {
            return $this->renderAjax('replace', [
                'sourceCamera' => $sourceCamera
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
        
        return new Camera();
    }
}
