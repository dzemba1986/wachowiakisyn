<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\Link;
use common\models\seu\devices\HostRfog;
use common\models\soa\Connection;
use frontend\modules\seu\models\forms\AddHostRfogForm;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;

class HostRfogController extends HostController {
    
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
        
        $model = new AddHostRfogForm();
        $connection = Connection::findOne($connectionId);
        
        if ($request->isPost) {
            //tworzy hosta pomimo wszystko
            if ($model->load($request->post()) && (is_null($hostId)) || $hostId == 'new') { 
                $transaction = Yii::$app->getDb()->beginTransaction();
                try {
                    $host = new HostRfog();
                    $link = new Link();
                    
                    $host->address_id = $connection->address_id;
                    $host->status = true;
                    $host->name = $connection->address->toString(true);
                    
                    if (!$host->save()) throw new Exception('Błąd zapisu host');
                    
                    $link->device = $host->id;
                    $link->port = 0;
                    $link->parent_device = $model->deviceId;
                    $link->parent_port = $model->port;
                    
                    if (!$link->save()) throw new Exception('Błąd zapisu linku');
                    
                    $connection->device_id = $model->deviceId;
                    $connection->port = $model->port;
                    $connection->host_id = $host->id;
                    $connection->conf_date = date('Y-m-d');
                    $connection->conf_user = Yii::$app->user->identity->id;
                    if ($connection->exec_date) $connection->exec_date = null;
                    
                    if (!$connection->save()) throw new Exception('błąd zapisu umowy');
                    
                } catch (\Throwable $t) {
                    $transaction->rollBack();
                    $t->getMessage();
                    exit();
                }
                
                $transaction->commit();
                $this->redirect(['link/index2', 'id' => $host->id . '.0']);
            //przypisuje umowę do nieaktywnego hosta
            } elseif ($model->load($request->post()) && is_int((int) $hostId)) { 
                $transaction = Yii::$app->getDb()->beginTransaction();
                try {
                    $host = HostRfog::findOne($hostId);
                    
                    $host->status = true;
                    
                    if (!$host->save()) throw new Exception('Błąd zapisu ip');
                    
                    $connection->host_id = $hostId;
                    $connection->conf_date = date('Y-m-d');
                    $connection->conf_user = Yii::$app->user->identity->id;
                    if ($connection->exec_date) $connection->exec_date = null;
                    
                    if (!$connection->save()) throw new Exception('błąd zapisu umowy');
                        
                } catch (\Throwable $t){
                    $transaction->rollBack();
                    echo $t->getMessage();
                    exit();
                }
                
                $transaction->commit();
                $this->redirect(['link/index2', 'id' => $host->id . '.0']);
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
                
                $this->redirect(['link/index2', 'id' => $hostId . '.0']);
            }
        } else {
            //znajdz wszystkie hosty z danego adresu i o danym systemie (ethernet czy rfog)
            $allHosts = HostRfog::find()->select('id, type_id, name, status')->where([
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
                
                return $this->renderAjax('add_new', [
                    'model' => $model,
                    'connectionId' => $connectionId,
                    'jsonType' => json_encode($connection->posibleParentTypeIds)
                ]);
            
            //znalazł pasujące hosty i chce przypisać umowę do aktywnego/nieaktywnego
            } elseif (!empty($hosts) && !is_null($hostId)) {
                $host = HostRfog::findOne($hostId);
                if ($host->status) {
                    return $this->renderAjax('add_active', [
                        'hostId' => $hostId
                    ]);
                } else {
                    $model->typeId = $connection->type_id;
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
    
    function actionAddHostValidation() {
        
        $request = Yii::$app->request;
        $model = new AddHostRfogForm();
        
        if ($model->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model, 'mac');
        }
    }
    
    protected static function classNameModel() {
        
        return HostRfog::className();
    }
}
