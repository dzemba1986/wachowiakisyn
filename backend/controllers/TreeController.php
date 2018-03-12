<?php

namespace backend\controllers;

use backend\models\Address;
use backend\models\Connection;
use backend\models\Device;
use backend\models\Host;
use backend\models\Ip;
use backend\models\Model;
use backend\models\Tree;
use backend\models\forms\AddHostForm;
use Yii;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\base\Exception;
use yii\db\Expression;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\validators\IpValidator;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;

class TreeController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class' => AjaxFilter::className(),
                'only' => ['add-host', 'get-children', 'move', 'copy', 'to-store', 'search', 'port-list']
            ],
        ];
    }

    public function actionIndex($id = null)
    {
        return $this->render('index', [
        	'id' => $id	
        ]);
    }
    
    /**
     * @param integer $hostId ID hosta (aktywnego/nieaktywnego) do którego mamy dodać umowę
     */
    public function actionAddHost($connectionId, $hostId = null) {
        
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
                    
                    if (!$connection->save()) throw new Exception('błąd zapisu umowy');
                    
                } catch (\Throwable $t) {
                    $transaction->rollBack();
                    var_dump($host->errors);
                    var_dump($link->errors);
                    var_dump($ip->errors);
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
                if (!in_array($connection->type_id, $host->connectionsType)) $hosts[] = $host;
            }
            //nie ma w ogóle hosta lub dodanie nowego pomimo znalezienia hostów
            if ((empty($hosts) && is_null($hostId)) || ($hostId == 'new' && !empty($hosts))) {
                $model->deviceId = $connection->device_id;
                $model->port = $connection->port;
                $model->typeId = $connection->type_id;
                $model->mac = $connection->mac;
                $model->address = $connection->address->toString();
                
                return $this->renderAjax('add_new_host', [
                    'model' => $model,
                    'connectionId' => $connectionId
                ]);
            //znalazł hosty i chce przypisać umowę do aktywnego/nieaktywnego
            } elseif (!empty($hosts) && !is_null($hostId)) {
                $host = Host::findOne($hostId);
                if ($host->status) {   
                    return $this->renderAjax('add_active_host', [
                        'hostId' => $hostId
                    ]);
                } else {
                    $model->typeId = $connection->type_id;
                    $model->mac = $connection->mac;
                    $model->address = $connection->address->toString();
                    
                    return $this->renderAjax('add_inactive_host', [
                        'model' => $model,
                        'hostId' => $hostId
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
    
    public function actionGetChildren($id) {
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $device = Device::findOne($id);
        $model = Model::findOne($device->model_id);
        
        $nodes = (new \yii\db\Query())
            ->select([
                new Expression("CASE WHEN proper_name IS NULL THEN concat(prefix, device.name) ELSE concat(prefix, device.name, '_', proper_name) END"),
                'agregation.device',
                'port',
                'parent_port',
                'model_id',
                'mac',
                'status',
                'device.type_id',
                'icon',
                'children'
            ])
            ->from('device')
            ->leftJoin('agregation', 'device.id = agregation.device')
            ->leftJoin('device_type', 'device_type.id = device.type_id')
            ->where(['parent_device' => $id])
            ->orderBy('parent_port')
            ->all();
        
        if (!empty($nodes)) {
        
            foreach ($nodes as $node){
            	
                $ips = Ip::find()->select('ip')->where(['device_id' => $node['device']])->asArray()->all();
                $text = $node['status'] ?
                    $model->port[$node['parent_port']].'	:<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i>'.$node['concat'] :
                    $model->port[$node['parent_port']].'	:<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i><font color="red">'.$node['concat'].'</font>';
                
            	$children[] = [
            		'id' => (int) $node['device'] . '.' . $node['port'],
            		'text' => $id != 1	? 
                        $text :
            			'<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i>'.$node['concat'],
            		'name' => $node['concat'],
            	    'network' => [
            	        'mac' => $node['mac'],
            	        'ips' => $ips,
            	    ],
            	    'type' => $node['type_id'],
            		'state' => $node['model_id'] == 5 ? ['opened' => true] : [], //dla centralnych automatyczne rozwijanie
            		'icon' => false,
            	    'children' => $node['children']
            	];
            }
        } else $children = [];
        
        return $children;
    }
    
    public function actionSearch($str) {
    
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	
    	//gdy dlugosc szukanego tekstu jest wieksza od 3
    	if (strlen($str) > 3){
	    	$path = [];
	    	
	    	$validatorIp = new IpValidator(['ipv6' => false]);
	    	$validatorMac = new MacaddressValidator();
	    	
	    	if ($validatorIp->validate($str)){
				$devices = Ip::find()->select('device_id AS id')->where(['ip' => $str])->asArray()->all();
	    	} elseif ($validatorMac->validate($str)){
	    	    $devices = Device::find()->select('id')->where(['and', ["CAST(mac AS varchar)" => $str], ['status' => true]])->asArray()->all();
	    	} else {
	    	    $devices = Device::find()->select('id')->where(['or', ['id' => (int) $str], ['like', 'name', strtoupper($str) . '%', false]])->andWhere(['is not', 'status', null])->asArray()->all();
	    	}
	    	
	    	//przejscie przez wszystkie wyszukane obiekty typu device 
	    	foreach ($devices as $device) {
	    		
	    		//powiazany element typu tree
	    		$modelTree = Tree::findOne(['device' => $device['id']]);
	    		
	    		//dopóki nie jest rootem
	    		while ($modelTree->parent_device <> 1) {
	    			$modelTree = Tree::findOne($modelTree->parent_device);
	    			//jezeli elementu tree rodzica nie ma w tablicy to dodaj 
	    			if (!in_array($modelTree->device . '.' . $modelTree->port, $path))
	    				array_push($path, $modelTree->device . '.' . $modelTree->port);
	    		}
	    	}
    	} else 
    		return null;
    	 
    	return array_reverse($path);
    }
    
    public function actionListPort($deviceId, $selected = null, $install = false, $mode = 'free') {
        
        $device = Device::findOne($deviceId);
        $model = $device->model;
        
        if ($mode == 'free') {
            $linksWithDevice = Tree::find()->select('parent_port')->where(['parent_device' => $deviceId])
                ->union(Tree::find()->select('port AS parent_port')->where(['device' => $deviceId]))->all();
            
            if (!empty($linksWithDevice)) {
                foreach ($linksWithDevice as $linkWithDevice) {
                    $usePorts[$linkWithDevice->parent_port] = $linkWithDevice->parent_port;
                }
                
                $freePorts = array_diff_key($model->port->getValue(), $usePorts);

                if ($install){
                    echo '<option value="-1">Brak miejsca</option>';
                }
                foreach ($freePorts as $key => $freePort ){
                    if ($selected == $key) {
                        echo '<option value="' . ($key) . '" selected="1">' . $freePort . '</option>';
                        continue;
                    }
                    echo '<option value="' . ($key) . '">' . $freePort . '</option>';
                }
            } else {
                echo '<option value="-1">Brak miejsca</option>';
            }
        } elseif ($mode == 'all') {
            echo '<option></option>';
            foreach ($model->port as $key => $port){
                echo '<option value="' . ($key) . '">' . $port . '</option>';
            }
        }
    }
    
    public function actionMove($deviceId, $port, $newParentId) {
    	
    	$request = Yii::$app->request;
    	
	    if($request->post('newParentPort')){
			
			$link = Tree::find()->where(['device' => $deviceId, 'port' => $port])->one();
			
			$link->parent_device = $newParentId;
			$link->parent_port = $request->post('newParentPort');
			
			try {
			    if (!$link->save()) throw new Exception('Błąd zapisu linku');
			} catch (\Throwable $t) {
			    var_dump($link->errors);
			    var_dump($t->getMessage());
			    exit();
			}
			
			return 1;
 		} else 
 		    return $this->renderAjax('move', [
 		        'newParentId' => $newParentId
 		    ]);
    }
    
    public function actionCopy($deviceId, $parentId) {
    	 
        $request = Yii::$app->request;
        
        if($request->post()){
            
            $link = new Tree();
            
            $link->device = $deviceId;
            $link->port = (int) $request->post('localPort');
            $link->parent_device = $parentId;
            $link->parent_port = (int) $request->post('parentPort');
            
            try {
                if (!$link->save()) throw new Exception('Błąd zapisu linku');
            } catch (\Throwable $t) {
                var_dump($link->errors);
                var_dump($t->getMessage());
                exit();
            }
            
            return 1;
        } else
            return $this->renderAjax('copy', [
                'deviceId' => $deviceId,
                'parentId' => $parentId
            ]);
    }
    
    function actionToStore($deviceId, $port) {
        
        $request = Yii::$app->request;
        $device = Device::findOne($deviceId);
        
        if($request->post()){
            
            $link = Tree::findOne(['device' => $deviceId, 'port' => $port]);
            $count = Tree::find()->where(['device' => $deviceId])->count();
            
            try {
                if (!$device->isParent()) {
                    $transaction = Yii::$app->getDb()->beginTransaction();
                    
                    if ($count == 1) {    //ostatnia kopia    
                        $device->address_id = 1;
                        $device->status = null;
                        $device->name = null;
                        $device->proper_name = null;
                        isset($device->alias) ? $device->alias = null : null;
                        
                        foreach ($device->ips as $ip)
                            if (!$ip->delete()) throw new Exception('Błąd usuwania IP');
                        
                        if (!$link->delete()) throw new Exception('Błąd usuwania agregacji');
                        if (!$device->save()) throw new Exception('Błąd zapisu urządzenia');
                    } else
                        if(!$link->delete()) throw new Exception('Błąd usuwania agregacji');
                                            
                } else return 'Urządzenie jest rodzicem';
                
                $transaction->commit();
                return 1;
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                var_dump($device->errors);
                var_dump($t->getMessage());
                exit();
            }
        } else
            return $this->renderAjax('to_store', [
                'device' => $device,
            ]);
    }
    
    public function actionReplace($deviceId)
    {
        $request = Yii::$app->request;
        
        if($request->isPost) {
            $map = $request->post('map');
            $destinationDevice = Device::findOne($request->post('destinationDeviceId'));
            $sourceDevice = Device::findOne($deviceId);
            $transaction = Yii::$app->getDb()->beginTransaction();
            
            try {
                foreach ($map as $oldPort => $newPort) {
                    $link = Tree::findOne(['parent_device' => $deviceId, 'parent_port' => $oldPort]);
                    
                    if (is_object($link)) {
                        $link->parent_device = $destinationDevice->id;
                        $link->parent_port = $newPort;
                        
                    } else {
                        $link = Tree::findOne(['device' => $deviceId, 'port' => $oldPort]);
                        if (!is_object($link)) throw new Exception('Nie znalazł linku');
                        
                        $link->device = $destinationDevice->id;
                        $link->port = $newPort;
                    }
                    
                    if (!$link->save()) throw new Exception('Błąd zapisu linku');
                }
                
                foreach ($sourceDevice->ips as $ip) {
                    $ip->device_id = $destinationDevice->id;
                    if (!$ip->save()) throw new Exception('Błąd zapisu ip');
                }
                
                $destinationDevice->address_id = $sourceDevice->address_id;
                $destinationDevice->status = $sourceDevice->status;
                $destinationDevice->name = $sourceDevice->name;
                
                $sourceDevice->address_id = 1;
                $sourceDevice->status = null;
                $sourceDevice->name = null;
                
                if (!($sourceDevice->save() && $destinationDevice->save())) throw new Exception('Błąd zapisu urządzenia');
                
            } catch (\Throwable $t) {
                $transaction->rollBack();
                echo $t->getMessage();
                exit();
            }
            
            $transaction->commit();
            return 1;
        } else {
            return $this->renderAjax('replace', [
                'deviceId' => $deviceId
            ]);
        }
    }
    
    public function actionReplacePort($sourceDeviceId, $destinationDeviceId) {
    	
        $sourceDevice = Device::findOne($sourceDeviceId);
        $destinationDevice = Device::findOne($destinationDeviceId);
        
    	$query1 = (Tree::find()->select(['device', 'parent_port AS port'])->where(['parent_device' => $sourceDeviceId]));
    	$query2 = (Tree::find()->select(['parent_device AS device', 'port'])->where(['device' => $sourceDeviceId]));
    	
    	$links =  (new \yii\db\Query())
    	->from(['result' => $query1->union($query2)])
    	->orderBy(['port' => SORT_ASC])->all();
    	
    	if ($sourceDevice->type_id != $destinationDevice->type_id) {
            return Html::tag('p', 'Wybrałeś urządzenie innego typu');
    	} elseif (!in_array($sourceDevice->type_id, [1,2,3,8])) {  //tylko typy do podmiany "1 do 1"
            return 'podmiana 1 do 1';   
        } else {
        	return $this->renderAjax('replace_port', [
        		'links' => $links,	
        		'sourceDevice' => $sourceDevice,
        		'destinationDeviceId' => $destinationDeviceId,
        	    'onetoone' => $sourceDevice->model_id == $destinationDevice->model_id ? true : false
        	]);
    	}
    }
    
    protected function findModel($id)
    {
        if (($model = Tree::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

