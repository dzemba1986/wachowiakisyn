<?php

namespace backend\controllers;

use backend\models\Ups;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Response;

class UpsController extends DeviceController
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
            ->where(['and', ['like', 'upper(d.name)', strtoupper($q) . '%', false], ['is not', 'status', null], ['d.type_id' => Ups::TYPE]])
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
            ->where(['and', ['address_id' => 1], ['d.type_id' => Ups::TYPE]])->andWhere([
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
    
    protected static function getModel() {
        
        return new Ups();
    }
}
