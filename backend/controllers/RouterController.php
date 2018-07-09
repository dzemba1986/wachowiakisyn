<?php

namespace backend\controllers;

use backend\models\Router;
use backend\models\Tree;
use Yii;
use yii\base\Exception;

class RouterController extends DeviceController
{
    public function actionReplace($id) {
        
        $request = Yii::$app->request;
        
        if($request->isPost) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            $sRouter = $this->findModel($id);
            $sRouter->scenario = Router::SCENARIO_REPLACE;
            $dRouter = $this->findModel($request->post('destinationDeviceId'));
            $dRouter->scenario = Router::SCENARIO_REPLACE;
            
            $map = $request->post('map');
            
            try {
                foreach ($map as $oldPort => $newPort) {
                    $link = Tree::findOne(['parent_device' => $id, 'parent_port' => $oldPort]);
                    
                    if (is_object($link)) {
                        $link->parent_device = $dRouter->id;
                        $link->parent_port = $newPort;
                        
                    } else {
                        $link = Tree::findOne(['device' => $id, 'port' => $oldPort]);
                        if (!is_object($link)) throw new Exception('Nie znalazł linku');
                        
                        $link->device = $dRouter->id;
                        $link->port = $newPort;
                    }
                    
                    if (!$link->save()) throw new Exception('Błąd zapisu linku');
                }
                
                foreach ($sRouter->ips as $ip) {
                    $ip->device_id = $dRouter->id;
                    if (!$ip->save()) throw new Exception('Błąd zapisu ip');
                }
                
                $dRouter->address_id = $sRouter->address_id;
                $dRouter->status = $sRouter->status;
                $dRouter->name = $sRouter->name;
                $dRouter->proper_name = $sRouter->proper_name;
                $dRouter->monitoring = $sRouter->monitoring;
                $dRouter->geolocation = $sRouter->geolocation;
                
                $sRouter->address_id = 1;
                $sRouter->status = null;
                $sRouter->name = null;
                $sRouter->proper_name = null;
                $sRouter->monitoring = false;
                $sRouter->geolocation = null;
                
                if (!($sRouter->save() && $dRouter->save())) throw new Exception('Błąd zapisu urządzenia');
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                echo $t->getMessage();
                var_dump($dRouter->errors);
                var_dump($sRouter->errors);
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
        
        return new Router();
    }
}
