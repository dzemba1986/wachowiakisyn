<?php

namespace backend\controllers;

use backend\models\Address;
use backend\models\Connection;
use backend\models\Device;
use backend\models\Host;
use backend\models\Ip;
use backend\models\Tree;
use backend\models\forms\AddHostForm;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

class HostController extends DeviceController
{
    public function behaviors() {
        
        return ArrayHelper::merge(
            parent::behaviors(),
            [   
                'access' => [
                    'class' => AccessControl::className(),
                    'rules'	=> [
                        [
                            'allow' => true,
                            'actions' => ['change-mac', 'get-change-mac-script', 'add-inactive-on-tree', 'add-host-validation', 'send-config'],
                            'roles' => ['@']
                        ]
                    ]
                ],
                [
                    'class' => AjaxFilter::className(),
                    'only' => ['send-config']
                ],
            ]
        );
    }
    
    /**
     * @param integer $hostId ID hosta (aktywnego/nieaktywnego) do którego mamy dodać umowę
     */
    public function actionAddOnTree($connectionId, $hostId = null) {
        
        $request = Yii::$app->request;
        
        $model = new AddHostForm();
        $connection = Connection::findOne($connectionId);
        
        if ($request->isPost) {
            if ($model->load($request->post()) && (is_null($hostId)) || $hostId == 'new') { //tworzy hosta pomimo wszystko
                $transaction = Yii::$app->getDb()->beginTransaction();
                try {
                    $host = new Host();
                    $link = new Tree();
                    $ip = new Ip();
                    
                    $host->mac = $model->mac;
                    $host->address_id = $connection->address_id;
                    $host->status = true;
                    $host->name = Address::findOne($connection->address_id)->toString(true);
                    
                    if (!$host->save()) throw new Exception('Błąd zapisu host');
                    
                    $link->device = $host->id;
                    $link->port = 0;
                    $link->parent_device = $model->deviceId;
                    $link->parent_port = $model->port;
                    
                    if (!$link->save()) throw new Exception('Błąd zapisu linku');
                    
                    $ip->ip = $model->ip;
                    $ip->subnet_id = $model->subnetId;
                    $ip->main = true;
                    $ip->device_id = $host->id;
                    
                    if (!$ip->save()) throw new Exception('Błąd zapisu ip');
                    
                    $connection->mac = $model->mac;
                    $connection->device_id = $model->deviceId;
                    $connection->port = $model->port;
                    $connection->host_id = $host->id;
                    $connection->conf_date = date('Y-m-d');
                    $connection->conf_user = Yii::$app->user->identity->id;
                    if ($connection->exec_date) $connection->exec_date = null;
                    
                    if (!$connection->save()) throw new Exception('błąd zapisu umowy');
                    
                } catch (\Throwable $t) {
                    $transaction->rollBack();
                    var_dump($connection->errors);
                    var_dump($host->errors);
                    var_dump($link->errors);
                    var_dump($ip->errors);
                    var_dump($t->getMessage());
                    exit();
                }
                
                $transaction->commit();
                $this->redirect(['tree/index', 'id' => $host->id . '.0']);
            } elseif ($model->load($request->post()) && is_int((int) $hostId)) { //przypisuje umowę do nieaktywnego hosta
                $transaction = Yii::$app->getDb()->beginTransaction();
                try {
                    $host = Host::findOne($hostId);
                    $ip = new Ip();
                    
                    $host->status = true;
                    $host->dhcp = true;
                    $host->smtp = false;
                    $host->mac = $model->mac;
                    
                    if (!$host->save()) throw new Exception('Błąd zapisu ip');
                    
                    $ip->ip = $model->ip;
                    $ip->subnet_id = $model->subnetId;
                    $ip->main = true;
                    $ip->device_id = $hostId;
                    
                    if (!$ip->save()) throw new Exception('Błąd zapisu ip');
                    
                    $connection->host_id = $hostId;
                    $connection->conf_date = date('Y-m-d');
                    $connection->conf_user = Yii::$app->user->identity->id;
                    if ($connection->exec_date) $connection->exec_date = null;
                    
                    if (!$connection->save())
                        throw new Exception('błąd zapisu umowy');
                        
                        $this->redirect(['tree/index', 'id' => $hostId . '.0']);
                } catch (\Throwable $t){
                    $transaction->rollBack();
                    var_dump($connection->errors);
                    var_dump($host->errors);
                    var_dump($ip->errors);
                    exit();
                }
                
                $transaction->commit();
                $this->redirect(['tree/index', 'id' => $host->id . '.0']);
            } elseif (is_int((int) $hostId)) { //przypisuje umowę do aktywnego hosta
                $connection->host_id = $hostId;
                $connection->conf_date = date('Y-m-d');
                $connection->conf_user = Yii::$app->user->identity->id;
                if ($connection->exec_date) $connection->exec_date = null;
                
                try {
                    if (!$connection->save())
                        throw new Exception('błąd zapisu umowy');
                } catch (\Throwable $t){
                    var_dump($connection->errors);
                    exit();
                }
                
                $this->redirect(['tree/index', 'id' => $hostId . '.0']);
            }
        } else {
            $allHosts = Host::find()->select('id, type_id, name, status')->where(['address_id' => $connection->address_id])->all();
            
            $hosts = [];
            foreach ($allHosts as $host) {
                if (!$host->status || !in_array($connection->type_id, $host->connectionsType)) $hosts[] = $host;
            }
            //nie ma w ogóle hosta lub dodanie nowego pomimo znalezienia hostów
            if ((empty($hosts) && is_null($hostId)) || ($hostId == 'new' && !empty($hosts))) {
                $model->deviceId = $connection->device_id;
                $model->port = $connection->port;
                $model->typeId = $connection->type_id;
                $model->mac = $connection->mac;
                $model->address = $connection->address->toString();
                
                return $this->renderAjax('add_new', [
                    'model' => $model,
                    'connectionId' => $connectionId
                ]);
                //znalazł hosty i chce przypisać umowę do aktywnego/nieaktywnego
            } elseif (!empty($hosts) && !is_null($hostId)) {
                $host = Host::findOne($hostId);
                if ($host->status) {
                    return $this->renderAjax('add_active', [
                        'hostId' => $hostId
                    ]);
                } else {
                    $model->typeId = $connection->type_id;
                    $model->mac = $connection->mac;
                    $model->address = $connection->address->toString();
                    
                    return $this->renderAjax('add_inactive', [
                        'model' => $model,
                        'host' => $host
                    ]);
                }
                //znalazł hosty i wyświetla wybór hostów lub dodania nowego
            } elseif (!empty($hosts) && is_null($hostId)) {
                return $this->renderAjax('add_choise', [
                    'hosts' => $hosts,
                    'connection' => $connection,
                ]);
            }
        }
    }
    
    public function actionAddInactiveOnTree($id) {
        
        $request = Yii::$app->request;
        $parent = Device::findOne($id);
        $host = new Host();
        $link = new Tree();
        $address = new Address();
        
        if ($link->load($request->post()) && $address->load($request->post())) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            
            try {
                if (!$address->save()) throw new Exception('Błąd zapisu adresu');
                
                $host->status = false;
                $host->address_id = $address->id;
                $host->name = $address->toString(true);
                $host->dhcp = false;
                $host->smtp = false;
                if (!$host->save()) throw new Exception('Błąd zapisu urządzenia');
                
                $link->parent_device = $id;
                $link->device = $host->id;
                $link->port = 0;
                if (!$link->save()) throw new Exception('Błąd zapisu drzewa');
                
                $transaction->commit();
                
                return 1;
            } catch (\Exception $e) {
                $transaction->rollBack();
                var_dump($host->errors);
                var_dump($address->errors);
                var_dump($link->errors);
                exit();
            }
        } else {
            return $this->renderAjax('add_inactive_on_tree', [
                'id' => $id,
                'link' => $link,
                'address' =>$address
            ]);
        }
    }
    
    function actionSendConfig($id, $type) {
        
        $request = Yii::$app->request;
        $host = $this->findModel($id);
        
        if ($request->isPost) {
            try {
                if ($type == 'drop') $host->configurationDrop(true);
                return 1;
            } catch (\Throwable $t) {
                return 0;    
            }
        } else {
            return $this->renderAjax('send_config', [
                'host' => $host
            ]);
        }
    }
    
    public function actionChangeMac($id) {
        
        $request = Yii::$app->request;
        $host = $this->findModel($id);
        $host->scenario = Host::SCENARIO_UPDATE;
        
        if ($host->load($request->post())) {
            try {
                if (!$host->save()) throw new Exception('Błąd zapisu mac');
                
                return 1;
            } catch (Exception $e) {
                var_dump($host->errors);
                exit();
            }
        } else {
            return $this->renderAjax('change_mac', [
                'host' => $host,
            ]);
        }
    }
    
    public function actionGetChangeMacScript($id, $newMac) {
        
        $host = $this->findModel($id);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $host->configurationChangeMac($newMac);
    }
    
    function actionAddHostValidation() {
        
        $request = Yii::$app->request;
        $model = new AddHostForm();
        
        if ($model->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model, 'mac');
        }
    }
    
    protected static function getModel() {
        
        return new Host();
    }
}
