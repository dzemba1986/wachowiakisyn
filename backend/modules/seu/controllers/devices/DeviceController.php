<?php

namespace backend\modules\seu\controllers\devices;

use backend\modules\address\models\Address;
use common\models\seu\Link;
use common\models\seu\devices\Device;
use common\models\seu\network\Ip;
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

abstract class DeviceController extends Controller
{
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
        $device = is_null($id) ? static::getModel() : $this->findModel($id);
        
        if ($device->load($request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($device);
        };
    }
    
	public function actionTabsView($id) {
	    
		return $this->renderPartial('tabs-view', [
			'id' => $id,
		]);
	}
	
	public function actionView($id) {
	    
	    $device = $this->findModel($id);

	    return $this->renderAjax('view', [
            'device' => $device,
        ]);
	}
	
	public function actionTabsUpdate($id)
	{
		return $this->renderPartial('tabs-update', [
			'id' => $id,
		]);
	}
	
	public function actionUpdate($id) {
	    
	    $device = $this->findModel($id);
	    $device->scenario = get_class($device)::SCENARIO_UPDATE;
	    $address = $device->address;
	    
	    $request = Yii::$app->request;
	    
	    if ($request->isPost) {
	        if ($device->load($request->post())) {
	            try {
	                if(!$device->save())
	                    throw new Exception('Problem z zapisem urządzenia');
	            } catch (Exception $e) {
	                var_dump($device->errors);
	                var_dump($e->getMessage());
	                exit();
	            }
	        }
	        
	        if ($address->load($request->post())) {
	            
	            $newAddress = new Address();
	            $newAddress->t_ulica = $address->t_ulica;
	            $newAddress->dom = $address->dom;
	            $newAddress->dom_szczegol = $address->dom_szczegol;
	            $newAddress->lokal = $address->lokal;
	            $newAddress->lokal_szczegol = $address->lokal_szczegol;
	            $newAddress->pietro = $address->pietro;
	            
	            try {
	                if(!$newAddress->save())
	                    throw new Exception('Problem z zapisem adresu');
	                    
	                    $device->address_id = $newAddress->id;
	                    $device->name = $newAddress->toString(true);
	                    if(!$device->save())
	                        throw new Exception('Problem z zapisem urządzenia');
	            } catch (Exception $e) {
	                var_dump($address->errors);
	                var_dump($e->getMessage());
	                exit();
	            }
	        }
	        return 1;
	    } else {
	        return $this->renderAjax('update', [
	            'device' => $device,
	            'address' => $address,
	        ]);
	    }
	}
	
    public final function actionListFromTree($q = null, $id = null, array $typeId = null) {
        
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        
        $out = ['results' => ['id' => '', 'concat' => '']];
        
        if ($q) {
            $query = new Query();
            if (!$typeId) {
                $query->select(['d.id', new Expression("CONCAT(d.name, ' - ', '[', ip, ']', ' - ', m.name)")])
        	    	->from('device d')
        	    	->join('INNER JOIN', 'model m', 'm.id = d.model_id')
        	    	->join('LEFT JOIN', 'ip', 'ip.device_id = d.id AND ip.main = true')
        	    	->where(['and', ['like', 'upper(d.name)', strtoupper($q) . '%', false], ['is not', 'status', null], ['d.type_id' => get_class(static::getModel())::TYPE]])
        	    	->orderBy('d.name');
            } else {
                $query->select(['d.id', new Expression("CONCAT(d.name, ' - ', '[', ip, ']', ' - ', m.name)")])
                    ->from('device d')
                    ->join('INNER JOIN', 'model m', 'm.id = d.model_id')
                    ->join('LEFT JOIN', 'ip', 'ip.device_id = d.id AND ip.main = true')
                    ->where(['and', ['like', 'upper(d.name)', strtoupper($q) . '%', false], ['is not', 'status', null], ['d.type_id' => $typeId]])
                    ->orderBy('d.name');
            }
	        $command = $query->createCommand();
	        $data = $command->queryAll();
	        $out['results'] = array_values($data);
        } elseif($id > 0) {
            $query = new Query();
            $query->select(['d.id', new Expression("CONCAT(d.name, ' - ', '[', ip, ']', ' - ', m.name)")])
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
			$query->select(['d.id', new \yii\db\Expression("CONCAT(m.name, ' - ', d.mac, ' - ', d.serial)")])
    	    	->from('device d')
    	    	->join('INNER JOIN', 'model m', 'm.id = d.model_id')
    	    	->where(['and', ['address_id' => 1], ['d.type_id' => get_class(static::getModel())::TYPE]])->andWhere([
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
	    
	    $device = $this->findModel($id);
	    $link = new Link();
	    $address = new Address();
	    if ($device->canHasIp) $ip = new Ip();

	    $request = Yii::$app->request;
	    
	    if ($link->load($request->post()) && $address->load($request->post())) {
	        $device->load($request->post());
	        $transaction = Yii::$app->getDb()->beginTransaction();
	        try {
	            if (!$address->save()) throw new Exception('Błąd zapisu adresu');
	            
	            $device->address_id = $address->id;
	            $device->name = $address->toString(true);
	            $device->addOnTree();
	            if (!$device->save()) throw new Exception('Błąd zapisu urządzenia');
	            
	            $link->device = $id;
	            if (!$link->save()) throw new Exception('Błąd zapisu drzewa');
	            
	            if ($device->canHasIp && $ip->load($request->post())) {
	                $ip->main = true;
	                $ip->device_id = $id;
	                if (!$ip->save()) throw new Exception('Błąd zapisu ip');
	            }
	            
	            $transaction->commit();
	            $this->redirect(['tree/index', 'id' => $device->id . '.' . $link->port]);
	        } catch (\Exception $e) {
	            $transaction->rollBack();
	            var_dump($device->errors);
	            var_dump($address->errors);
	            var_dump($link->errors);
	            if ($device->canHasIp) var_dump($ip->errors);
	            exit();
	        }
	    } else {
	        $param = [
	            'device' => $device,
	            'link' => $link,
	            'address' => $address,
	        ];
	        if ($device->canHasIp) $param['ip'] = $ip;
	        
	        return $this->renderAjax('add_on_tree', $param);
	    }
	}
	
	function actionDeleteFromTree($id, $port) {
	    
	    $request = Yii::$app->request;
	    $device = $this->findModel($id);
	    
	    if($request->isPost) {
	        try {
	            if (!($device->canBeParent && $device->isParent())) {
	                $link = Link::findOne(['device' => $id, 'port' => $port]);
	                $count = Link::find()->where(['device' => $id])->count();
	                $transaction = Yii::$app->getDb()->beginTransaction();
	                
	                if ($count == 1) {    //ostatnia kopia
	                    $device->deleteFromTree();
	                    
	                    if ($device->canHasIp)
	                        foreach ($device->ips as $ip) if (!$ip->delete()) throw new Exception('Błąd usuwania IP');
	                        
                        if (!$link->delete()) throw new Exception('Błąd usuwania agregacji');
                        if (!$device->save()) throw new Exception('Błąd zapisu urządzenia');
	                } else
	                    if(!$link->delete()) throw new Exception('Błąd usuwania agregacji');
	                    
	            } else $out = 'Urządzenie jest rodzicem';
	            
	            $transaction->commit();
	            $out = 1;
	            
	            return $out;
	            
	        } catch (\Throwable $t) {
	            $transaction->rollBack();
	            var_dump($t->getMessage());
	            exit();
	        }
	    } else
	        return $this->renderAjax('@app/views/device/delete_from_tree');
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
	    
	    $device = static::getModel();
	    $device->scenario = get_class($device)::SCENARIO_CREATE;
	    
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
	    $device->scenario = get_class($device)::SCENARIO_UPDATE;
	    
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
            return $this->renderAjax('update_store', [
                'device' => $device
            ]);
        }
	}
	
	public function actionDeleteFromStore($id)
	{
	    $request = Yii::$app->request;
	    
        if($request->isPost){
            if (!$this->findModel($id)->delete()) return 'Błąd usuwania urządzenia';
            return 1;
        } else{
            return $this->renderAjax('@app/views/device/delete_from_store');
        }
	}
	
	protected abstract static function getModel();

    protected function findModel($id) {
        
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function findModelAsArray($id) {
        
        if (($model = Device::find()->where(['id' => $id])->asArray()->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
