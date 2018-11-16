<?php

namespace frontend\modules\seu\controllers\devices;

use backend\modules\address\models\Address;
use common\models\seu\Link;
use common\models\seu\devices\Host;
use common\models\seu\devices\HostEthernet;
use common\models\seu\network\Ip;
use common\models\soa\Connection;
use frontend\modules\seu\models\forms\AddHostEthernetForm;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;


class HostEthernetController extends HostController {
    
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
        
        $model = new AddHostEthernetForm();
        $connection = Connection::findOne($connectionId);
        
        if ($request->isPost) {
            //tworzy hosta pomimo wszystko
            if ($model->load($request->post()) && (is_null($hostId)) || $hostId == 'new') { 
                $transaction = Yii::$app->getDb()->beginTransaction();
                try {
                    $host = new HostEthernet();
                    $link = new Link();
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
                    echo $t->getMessage();
                    exit();
                }
                
                $transaction->commit();
                $this->redirect(['link/index', 'id' => $host->id . '.0']);
            //przypisuje umowę do nieaktywnego hosta
            } elseif ($model->load($request->post()) && is_int((int) $hostId)) { 
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
                    
                    if (!$connection->save()) throw new Exception('błąd zapisu umowy');
                        
                } catch (\Throwable $t){
                    $transaction->rollBack();
                    echo $t->getMessage();
                    echo $t->getFile() . $t->getLine();
                    exit();
                }
                
                $transaction->commit();
                $this->redirect(['link/index', 'id' => $host->id . '.0']);
            //przypisuje umowę do aktywnego hosta
            } elseif (is_int((int) $hostId)) { 
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
                
                $this->redirect(['link/index', 'id' => $hostId . '.0']);
            }
        } else {
            //znajdz wszystkie hosty z danego adresu i o danym systemie (ethernet czy rfog)
            $allHosts = Host::find()->select('id, type_id, name, status, technic')->where([
                'address_id' => $connection->address_id, 
                'technic' => $connection->technic
            ])->all();
            
            //wybierz spoód wyszukanych tylko te hosty które są nieaktywne lub nie mają umowy o tym typie co własnie dodawana
            $hosts = [];
            foreach ($allHosts as $host) if (!$host->status || !in_array($connection->type_id, $host->connectionsType)) $hosts[] = $host;
            
            //brak pasujących hostów lub dodanie nowego pomimo znalezienia hostów
            if ((empty($hosts) && is_null($hostId)) || ($hostId == 'new' && !empty($hosts))) {
                $model->deviceId = $connection->device_id;
                $model->port = $connection->port;
                $model->typeId = $connection->type_id;
                $model->mac = $connection->mac;
                
                return $this->renderAjax('add_new', [
                    'model' => $model,
                    'connection' => $connection,
                    'jsonType' => json_encode($connection->posibleParentTypeIds)
                ]);
            
            //znalazł pasujące hosty i chce przypisać umowę do aktywnego/nieaktywnego
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
        $host = new HostEthernet();
        $link = new Link();
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
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            try {
                if ($type == 'drop') $host->configDrop(true);
                return [1, $host->parent->getSnmpVlan()];
            } catch (\Throwable $t) {
                var_dump($t->getMessage());
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
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $host = $this->findModel($id);
        
        return $host->configChangeMac($newMac);
    }
    
    function actionAddHostValidation() {
        
        $request = Yii::$app->request;
        $model = new AddHostEthernetForm();
        
        if ($model->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model, 'mac');
        }
    }
    
    protected static function classNameModel() {
        
        return HostEthernet::className();
    }
}
