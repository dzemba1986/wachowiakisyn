<?php

namespace backend\controllers;

use backend\models\Server;
use backend\models\Tree;
use Yii;
use yii\base\Exception;

class ServerController extends DeviceController
{	
    public function actionReplace($id) {
        
        $request = Yii::$app->request;
        
        if($request->isPost) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            $sourceMc = $this->findModel($id);
            $sourceMc->scenario = Server::SCENARIO_REPLACE;
            $destinationMc = $this->findModel($request->post('destinationDeviceId'));
            $destinationMc->scenario = Server::SCENARIO_REPLACE;
            
            try {
                $link = Tree::findOne(['device' => $id]);
                if (!is_object($link)) throw new Exception('Nie znalazł linku');
                $link->device = $destinationMc->id;
                if (!$link->save()) throw new Exception('Błąd zapisu linku');
                
                $destinationMc->address_id = $sourceMc->address_id;
                $destinationMc->status = $sourceMc->status;
                $destinationMc->name = $sourceMc->name;
                $destinationMc->proper_name = $sourceMc->proper_name;
                
                $sourceMc->address_id = 1;
                $sourceMc->status = null;
                $sourceMc->name = null;
                $sourceMc->proper_name = null;
                
                if (!($sourceMc->save() && $destinationMc->save())) throw new Exception('Błąd zapisu urządzenia');
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                echo $t->getMessage();
                var_dump($destinationMc->errors);
                var_dump($sourceMc->errors);
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
        
        return new Server();
    }
}
