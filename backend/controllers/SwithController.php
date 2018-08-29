<?php

namespace backend\controllers;

use backend\models\Swith;
use backend\models\Tree;
use Yii;
use yii\base\Exception;
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
    
    public function actionReplace($id) {
        
        $request = Yii::$app->request;
        
        if($request->isPost) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            $sSwitch = $this->findModel($id);
            $sSwitch->scenario = Swith::SCENARIO_REPLACE;
            $dSwitch = $this->findModel($request->post('destinationDeviceId'));
            $dSwitch->scenario = Swith::SCENARIO_REPLACE;
            
            $map = $request->post('map');
            
            try {
                foreach ($map as $oldPort => $newPort) {
                    $link = Tree::findOne(['parent_device' => $id, 'parent_port' => $oldPort]);
                    
                    if (is_object($link)) {
                        $link->parent_device = $dSwitch->id;
                        $link->parent_port = $newPort;
                        
                    } else {
                        $link = Tree::findOne(['device' => $id, 'port' => $oldPort]);
                        if (!is_object($link)) throw new Exception('Nie znalazł linku');
                        
                        $link->device = $dSwitch->id;
                        $link->port = $newPort;
                    }
                    
                    if (!$link->save()) throw new Exception('Błąd zapisu linku');
                }
                
                foreach ($sSwitch->ips as $ip) {
                    $ip->device_id = $dSwitch->id;
                    if (!$ip->save()) throw new Exception('Błąd zapisu ip');
                }
                
                $dSwitch->address_id = $sSwitch->address_id;
                $dSwitch->status = $sSwitch->status;
                $dSwitch->name = $sSwitch->name;
                $dSwitch->proper_name = $sSwitch->proper_name;
                $dSwitch->monitoring = $sSwitch->monitoring;
                $dSwitch->geolocation = $sSwitch->geolocation;
                
                $sSwitch->address_id = 1;
                $sSwitch->status = null;
                $sSwitch->name = null;
                $sSwitch->proper_name = null;
                $sSwitch->monitoring = false;
                $sSwitch->geolocation = null;
                
                if (!($sSwitch->save() && $dSwitch->save())) throw new Exception('Błąd zapisu urządzenia');
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                echo $t->getMessage();
                var_dump($dSwitch->errors);
                var_dump($sSwitch->errors);
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
    
    protected static function getModel() {
        
        return new Swith();
    }
}
