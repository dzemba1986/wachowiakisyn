<?php

namespace backend\controllers;

use backend\models\Address;
use backend\models\Device;
use backend\models\Ip;
use backend\models\MediaConverter;
use backend\models\Tree;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Response;

class MediaConverterController extends DeviceController
{
    public function actionListFromTree($q = null, $id = null) {
        
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'concat' => '']];
        
        if (!is_null($q)) {
            $query = new Query();
            $query->select(['d.id', new Expression("CONCAT(d.name, ' - ', m.name)")])
            ->from('device d')
            ->join('INNER JOIN', 'model m', 'm.id = d.model_id')
            ->where(['and', ['like', 'upper(d.name)', strtoupper($q) . '%', false], ['is not', 'status', null], ['d.type_id' => MediaConverter::TYPE]])
            ->orderBy('d.name');
            
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif($id > 0) {
            $query = new Query();
            $query->select(['d.id', new Expression("CONCAT(d.name, ' - ', m.name)")])
            ->from('device d')
            ->join('INNER JOIN', 'model m', 'm.id = d.model_id')
            ->where(['d.id' => $id]);
            
            $command = $query->createCommand();
            $data = $command->queryAll();
            
            $out['results'] = ['id' => $data[0]['id'], 'concat' => $data[0]['concat']];
        }
        
        return $out;
    }
    
    public final function actionListFromStore($q = null) {
        
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'concat' => '']];
        
        if (!is_null($q)) {
            $query = new Query();
            $query->select(['d.id', new \yii\db\Expression("CONCAT(m.name, ' - ', d.serial)")])
            ->from('device d')
            ->join('INNER JOIN', 'model m', 'm.id = d.model_id')
            ->where(['and', ['address_id' => 1], ['d.type_id' => MediaConverter::TYPE]])->andWhere([
                'or',
                ['like', new Expression("CAST(mac AS varchar)"), $q],
                ['like', 'upper(d.serial)', strtoupper($q)],
                ['like', 'lower(m.name)', strtolower($q)],
            ]);
            
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        
        return $out;
    }
    
    public function actionAddOnTree($id) {
        
        $request = Yii::$app->request;
        $device = Device::findOne($id);
        $link = new Tree();
        $address = new Address();
        $ip = new Ip();
        
        if ($link->load($request->post()) && $address->load($request->post())) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            
            $link->device = $id;
            $device->status = true;
            try {
                if (!$address->save()) throw new Exception('Błąd zapisu adresu');
                
                $device->address_id = $address->id;
                $device->name = $address->toString(true);
                if (!$device->save()) throw new Exception('Błąd zapisu urządzenia');
                
                if (!$link->save()) throw new Exception('Błąd zapisu drzewa');
                
                $transaction->commit();
                $this->redirect(['tree/index', 'id' => $device->id . '.' . $link->port]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                var_dump($device->errors);
                var_dump($address->errors);
                var_dump($link->errors);
                exit();
            }
        } else {
            return $this->renderAjax('add_on_tree', [
                'id' => $id,
                'link' => $link,
                'address' => $address,
            ]);
        }
    }
    
    public function actionReplace($id) {
        
        $request = Yii::$app->request;
        
        if($request->isPost) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            $sourceMc = $this->findModel($id);
            $sourceMc->scenario = MediaConverter::SCENARIO_REPLACE;
            $destinationMc = $this->findModel($request->post('destinationDeviceId'));
            $destinationMc->scenario = MediaConverter::SCENARIO_REPLACE;
            
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
        
        return new MediaConverter();
    }
}
