<?php

namespace backend\controllers;

use backend\models\Device;
use backend\models\Tree;
use backend\models\Virtual;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class VirtualController extends DeviceController
{	
    public function behaviors() {
        
        return ArrayHelper::merge(
            parent::behaviors(),
            ['access' => [
                'class' => AccessControl::className(),
                'rules'	=> [
                    [
                        'allow' => true,
                        'actions' => ['change-mac', 'get-change-mac-script'],
                        'roles' => ['@']
                    ]
                ]
            ]]
            );
    }
    
    public function actionChangeMac($id) {
        
        $request = Yii::$app->request;
        $virtual = $this->findModel($id);
        $virtual->scenario = Virtual::SCENARIO_UPDATE;
        
        if ($virtual->load($request->post())) {
            try {
                if (!$virtual->save()) throw new Exception('Błąd zapisu mac');
                
                return 1;
            } catch (Exception $e) {
                var_dump($virtual->errors);
                exit();
            }
        } else {
            return $this->renderAjax('change_mac', [
                'virtual' => $virtual,
            ]);
        }
    }
    
    public function actionGetChangeMacScript($id, $newMac) {
        
        $request = Yii::$app->request;
        $host = $this->findModel($id);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $host->configurationChangeMac($newMac);
    }
    
    public function actionAddOnTree($id) {
        
        $request = Yii::$app->request;
        $parent = Device::findOne($id);
        $virtual = new Virtual();
        $link = new Tree();
        
        if ($link->load($request->post()) && $virtual->load($request->post())) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            
            try {
                $virtual->status = true;
                $virtual->address_id = $parent->address->id;
                $virtual->name = $parent->name;
                if (!$virtual->save()) throw new Exception('Błąd zapisu urządzenia');
                
                $link->parent_device = $id;
                $link->device = $virtual->id;
                $link->port = 0;
                if (!$link->save()) throw new Exception('Błąd zapisu drzewa');
                
                $transaction->commit();
                $this->redirect(['tree/index', 'id' => $virtual->id . '.0']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                var_dump($virtual->errors);
                var_dump($address->errors);
                var_dump($link->errors);
                exit();
            }
        } else {
            return $this->renderAjax('add_on_tree', [
                'id' => $id,
                'link' => $link,
                'virtual' => $virtual
            ]);
        }
    }
    
    protected static function getModel() {
        
        return new Virtual();
    }
}
