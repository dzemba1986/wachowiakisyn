<?php
namespace frontend\controllers;

use backend\models\Camera;
use yii\db\Query;
use yii\web\Controller;

class DeviceController extends Controller {
    
    function actionListCamera($q) {
        
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
            ])->limit(50)->orderBy('d.alias');
            
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        
        return $out;
    }
}