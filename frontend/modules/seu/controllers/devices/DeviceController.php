<?php

namespace frontend\modules\seu\controllers\devices;

use backend\modules\address\models\Address;
use common\models\seu\Link;
use common\models\seu\devices\Device;
use frontend\modules\seu\models\forms\AddDeviceOnTreeForm;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\models\seu\network\Ip;

class DeviceController extends Controller {
    
    public function getViewPath() {
        
        return Yii::getAlias('@app/modules/seu/views/devices/' . $this->id);
    }
    
    public function behaviors() {
        
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules'	=> [
                    [
                        'allow' => true,
                        'actions' => [
                            'tabs-view', 'tabs-update', 'view', 'update', 'validation', 'list-from-tree', 'list-from-store', 'add-on-tree', 'delete-from-tree', 
                            'add-on-store', 'update-store', 'delete-from-store', 'replace', 'replace-port'
                        ],
                        'roles' => ['@']
                    ]
                ]
            ],
            [
                'class' => AjaxFilter::className(),
                'only' => ['view', 'update', 'list-from-tree', 'update-store']
            ],
        ];
    }
    
    function actionValidation($id = null) {
        
        $request = Yii::$app->request;
        $device = is_null($id) ? Yii::createObject(['class' => static::classNameModel()]) : $this->findModel($id);
        
        if ($device->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($device);
        };
    }
    
	public function actionTabsView($id) {
	    
		return $this->renderPartial('/devices/device/tabs-view', [
			'id' => $id,
		]);
	}
	
	public function actionView($id) {
	    
	    $device = $this->findModel($id);

	    return $this->renderAjax('view', [
            'device' => $device,
        ]);
	}
	
	public function actionTabsUpdate($id) {
	    
		return $this->renderPartial('/devices/device/tabs-update', [
			'id' => $id,
		]);
	}
	
	public function actionUpdate($id) {
	    
	    $device = $this->findModel($id);
	    $address = $device->address;
	    
	    $request = Yii::$app->request;
	    
	    if ($request->isPost && $device->load($request->post())) {
            $isValid = true;
            
            if ($address->load($request->post())) {
                $addressChange = !empty($address->getDirtyAttributes()) ? true : false;
                if ($addressChange) {
                    $newAddress = new Address();
                    $newAddress->t_ulica = $address->t_ulica;
                    $newAddress->dom = $address->dom;
                    $newAddress->dom_szczegol = $address->dom_szczegol;
                    $newAddress->lokal = $address->lokal;
                    $newAddress->lokal_szczegol = $address->lokal_szczegol;
                    $newAddress->pietro = $address->pietro;
                    
                    $isValid = $newAddress->validate();
                }
            }
            $isValid = $device->validate() && $isValid;
            
            try {
//                 var_dump($isValid); exit();
                if ($isValid) {
                    $device->scenario = $device::className()::SCENARIO_UPDATE;
                    $transaction = Yii::$app->getDb()->beginTransaction();
                    if ($addressChange) {
                        $newAddress->save(false);
                        $device->address_id = $newAddress->id;
                        $device->name = $newAddress->toString(true);
                    }
                    $device->save(false);
                    
                    $transaction->commit();
                    $out = 1;
                } else $out = 'Błąd aktualizacji urządzenia';
                
                return $out;   
            } catch (Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage();
            }
        } else {
	        return $this->renderAjax('update', [
	            'device' => $device,
	            'address' => $address,
	        ]);
	    }
	}
	
    public final function actionListFromTree($q = null, $id = null, array $type = null) {
        
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'concat' => '']];
        
        if ($q) {
            $query = new Query();
                $query->select(['d.id', new Expression("CASE WHEN ip IS NOT NULL THEN CONCAT(d.name, ' - [', ip, '] - ', m.name) ELSE CONCAT(d.name, ' - ', m.name) END")])
        	    	->from('device d')
        	    	->join('INNER JOIN', 'model m', 'm.id = d.model_id')
        	    	->join('LEFT JOIN', 'ip', 'ip.device_id = d.id AND ip.main = true')
        	    	->where([
        	    	    'and', 
        	    	    ['like', 'upper(d.name)', strtoupper($q) . '%', false], 
        	    	    ['is not', 'status', null], 
        	    	    ['d.type_id' => $type]
        	    	])
        	    	->orderBy('d.name');
	        $command = $query->createCommand();
	        $data = $command->queryAll();
	        $out['results'] = array_values($data);
        } elseif($id > 0) {
            $query = new Query();
            $query->select(['d.id', new Expression("CASE WHEN ip IS NOT NULL THEN CONCAT(d.name, ' - [', ip, '] - ', m.name) ELSE CONCAT(d.name, ' - ', m.name) END")])
                ->from('device d')
                ->join('INNER JOIN', 'model m', 'm.id = d.model_id')
                ->join('LEFT JOIN', 'ip', 'ip.device_id = d.id AND ip.main = true')
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
			$query->select(['d.id', new \yii\db\Expression("CASE WHEN mac IS NOT NULL THEN CONCAT(m.name, ' - ', d.mac, ' - ', d.serial) ELSE CONCAT(m.name, ' - ', d.serial) END")])
    	    	->from('device d')
    	    	->join('INNER JOIN', 'model m', 'm.id = d.model_id')
    	    	->where(['address_id' => 1])->andWhere([
    	    	    'or',
    	    	    ['like', 'lower(m.name)', strtolower($q)],
    	    	    ['like', new Expression('lower("mac"::text)'), strtolower($q)], 
    	    	    ['like', 'upper(d.serial)', strtoupper($q)], 
    	    	]);
	
    		$command = $query->createCommand();
    		$data = $command->queryAll();
    		$out['results'] = array_values($data);
		}
		
		return $out;
	}
	
	public function actionAddOnTree($parentId, $childId = null) {
	    
	    $request = Yii::$app->request;
	    
	    $model = new AddDeviceOnTreeForm();
	    if ($request->isPost && $model->load($request->post())) {
	        if ($model->add() === 1) {
	            return 1;
	        } else 
               return 0;
	    } else {
	        //brak wybranego urządzenia
	        if (!$childId) {
	            return $this->renderAjax('select_device', [
	                'model' => $model,
	                'parentId' => $parentId
	            ]);
            //urządzenie wybrane
	        } else {
	            $childDevice = Device::find()->select('type_id')->where(['id' => $childId])->asArray()->one();
	            
    	        return $this->renderPartial('/devices/' . Device::getController($childDevice['type_id']) . '/add_on_tree', [
    	            'model' => $model,
    	            'parentId' => $parentId
    	        ]);
	        }
	    }
	}
	
	function actionDeleteFromTree($id, $port) {
	    
	    $request = Yii::$app->request;
	    $device = $this->findModel($id);
	    
	    if($request->isPost) {
	        try {
	            $transaction = Yii::$app->getDb()->beginTransaction();
	            
	            if (!($device->canBeParent && $device->isParent())) {
	                $out = 1;
	                $count = Link::find()->where(['device' => $id])->count();
	                
	                if ($count == 1) {    //ostatnia kopia
	                    if ($device->canHasIp) Ip::deleteAll(['device_id' => $id]);
	                    if (Link::deleteAll(['device' => $id, 'port' => $port]) == 0) $out = 'Nie usunięto połączenia';    
                        $device->deleteFromTree();
                        if (!$device->save()) $out = 'Błąd zapisu urządzenia';
	                } else
	                    if (Link::deleteAll(['device' => $id, 'port' => $port]) == 0) $out = 'Nie usunięto połączenia';
	                    
                    if ($out === 1) $transaction->commit();
                    
	            } else $out = 'urządzenie jest rodzicem';
	            
	            return $out;
	            
	        } catch (\Throwable $t) {
	            $transaction->rollBack();
	            var_dump($t->getMessage());
	            exit();
	        }
	    } else
	        return $this->renderAjax('/devices/device/delete_from_tree');
	}
	
	public function actionReplace($id) {
	    
	    $request = Yii::$app->request;
	    $source = $this->findModel($id);
	    if($request->isPost) {
	        $transaction = Yii::$app->getDb()->beginTransaction();
	        try {
	            $post = $request->post();
	            $source->replace($post);
	            
	        } catch (\Throwable $t) {
	            $transaction->rollBack();
	            echo $t->getMessage();
	            exit();
	        }
	        $transaction->commit();
	        return 1;
	    } else {
	        return $this->renderAjax('replace', [
	            'source' => $source
	        ]);
	    }
	}
	
	public function actionAddOnStore() {
	    
	    $device = Yii::createObject(['class' => static::classNameModel()]);
	    $device->scenario = $device::className()::SCENARIO_CREATE;
	    
	    $request = Yii::$app->request;
	    
	    if ($device->load($request->post())) {
	        
	        $device->status = null;
	        $device->address_id = 1;
	        
	        try {
	            if(!$device->save()) throw new Exception('Błąd zapisu urządzenia');
	            
	            return 1;
	        } catch (Exception $e) {
	            var_dump($device->errors);
	            var_dump($e->getMessage());
	            exit();
	        }
	        
	    } else {
            return $this->renderAjax('add_on_store', [
                'device' => $device,
            ]);
	    }
	}
	
	public function actionUpdateStore($id)
	{
	    $device = $this->findModel($id);
	    $device->scenario = $device::className()::SCENARIO_UPDATE;
	    
	    $request = Yii::$app->request;
	    
        if($device->load($request->post())){
            try {
                if(!$device->save()) throw new Exception('Problem z zapisem urządzenia');
                
                return 1;
            } catch (Exception $e) {
                var_dump($device->errors);
                exit();
            }
        } else {
            return $this->renderAjax('/devices/device/update_store', [
                'device' => $device
            ]);
        }
	}
	
	public function actionDeleteFromStore($id) {
	    
	    $request = Yii::$app->request;
	    
        if($request->isPost) {
            if (Device::deleteAll(['id' => $id]) === 0) return 'Błąd usuwania urządzenia';
            return 1;
        } else return $this->renderAjax('/devices/device/delete_from_store');
	}
	
    protected function findModel($id) {
        
        if (($model = static::classNameModel()::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected static function classNameModel() {
        
        return Device::className();
    }
}
