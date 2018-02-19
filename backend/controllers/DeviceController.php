<?php

namespace backend\controllers;

use backend\models\Address;
use backend\models\Device;
use backend\models\DeviceSearch;
use backend\models\Host;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DeviceController extends Controller
{
	public function actionTabsView($id)
	{
		return $this->renderPartial('tabs-view', [
			'device' => $this->findModel($id),
		]);
	}
	
	public function actionTabsUpdate($id)
	{
		return $this->renderPartial('tabs-update', [
			'device' => $this->findModel($id),
		]);
	}
	
	public function actionView($id)
    { 	
	    $device = $this->findModel($id);
        
	    switch (get_class($device)){
	        case 'backend\models\Host':
	            return $this->renderAjax('view_host', [
	               'device' => $device,
	            ]);
	            break;
	        case 'backend\models\Router':
	            return $this->renderAjax('view_router', [
	               'device' => $device,
	            ]);
	            break;
	        case 'backend\models\Swith':
	            
	            return $this->renderAjax('view_switch', [
	               'device' => $device,
	            ]);
	            break;
	        case 'backend\models\GatewayVoip':
	            
	            return $this->renderAjax('view_gateway_voip', [
	               'device' => $device,
	            ]);
	            break;
	        case 'backend\models\Camera':
	            
	            return $this->renderAjax('view_camera', [
	               'device' => $device,
	            ]);
	            break;
	        case 'backend\models\Server':
	            
	            return $this->renderAjax('view_server', [
	               'device' => $device,
	            ]);
	            break;
	        case 'backend\models\Virtual':
	            
	            return $this->renderAjax('view_virtual', [
	               'device' => $device,
	            ]);
	            break;
	        case 'backend\models\MediaConverter':
	            
	            return $this->renderAjax('view_media_converter', [
	               'device' => $device,
	            ]);
	            break;
	    }
    }
    
    public function actionChangeMac($hostId) {
        
        $request = Yii::$app->request;
        $device = $this->findModel($hostId);
        $device->scenario = Host::SCENARIO_UPDATE;
        
        if ($device->load($request->post())) {
            try {
                if (!$device->save()) throw new Exception('Błąd zapisu mac');
                
                return 1;
            } catch (Exception $e) {
                var_dump($device->errors);
                exit();
            }
        } else {
            return $this->renderAjax('change_mac', [
                'device' => $device,
            ]);
        }
    }
    
    public function actionGetChangeMacScript($deviceId, $newMac) {
        
        $request = Yii::$app->request;
        $device = $this->findModel($deviceId);
        $device->scenario = Host::SCENARIO_UPDATE;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $device->configurationChangeMac($newMac);
    }
    
    public function actionStore()
    {
        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        
        $dataProvider->query->andWhere(['address' => NULL]);

        return $this->renderAjax('store', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionUpdate($id) {
        
        $device = $this->findModel($id);
        $device->scenario = Device::SCENARIO_UPDATE;
        $address = $device->address;
        
        $request = Yii::$app->request;
        
        if ($request->isAjax) {
            if ($request->isPost) {
                if ($device->load($request->post())) {
                    try {
                        if(!$device->save())
                            throw new \Exception('Problem z zapisem urządzenia');
                    } catch (\Exception $e) {
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
                            throw new \Exception('Problem z zapisem adresu');
                        
                        $device->address_id = $newAddress->id;
                        $device->name = $newAddress->toString(true);
                        if(!$device->save())
                            throw new \Exception('Problem z zapisem urządzenia');
                    } catch (\Exception $e) {
                        var_dump($address->errors);
                        var_dump($e->getMessage());
                        exit();
                    }
                }
                
                return 1;
            } else {
                switch (get_class($device)){
                    case 'backend\models\Host':
                        return $this->renderAjax('update_host', [
                            'device' => $device,
                            'address' => $address,
                        ]);
                        break;
                    case 'backend\models\Router':
                        return $this->renderAjax('update_router', [
                            'device' => $device,
                            'address' => $address,
                        ]);
                        break;
                    case 'backend\models\Swith':
                        
                        return $this->renderAjax('update_switch', [
                            'device' => $device,
                            'address' => $address,
                        ]);
                        break;
                    case 'backend\models\GatewayVoip':
                        
                        return $this->renderAjax('update_gateway_voip', [
                            'device' => $device,
                            'address' => $address,
                        ]);
                        break;
                    case 'backend\models\Camera':
                        
                        return $this->renderAjax('update_camera', [
                            'device' => $device,
                            'address' => $address,
                        ]);
                        break;
                    case 'backend\models\Server':
                        
                        return $this->renderAjax('update_server', [
                            'device' => $device,
                            'address' => $address,
                        ]);
                        break;
                    case 'backend\models\Virtual':
                        
                        return $this->renderAjax('update_virtual', [
                            'device' => $device,
                            'address' => $address,
                        ]);
                        break;
                    case 'backend\models\MediaConverter':
                        
                        return $this->renderAjax('update_media_converter', [
                            'device' => $device,
                            'address' => $address,
                        ]);
                        break;
                }
            }
        }
    }

    /**
     * Deletes an existing Modyfication model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionListFromTree($q = null, $id = null, array $type_id = []) {
        
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        
        if ($request->isAjax) {
            $out = ['results' => ['id' => '', 'concat' => '']];
            
            if (!is_null($q)) {
                $query = new Query();
                $query->select(['d.id', new \yii\db\Expression("
		    		CONCAT(d.name, ' - ', '[', ip, ']', ' - ', m.name)
		    	")])
		    	->from('device d')
		    	->join('INNER JOIN', 'model m', 'm.id = d.model_id')
		    	->join('LEFT JOIN', 'ip', 'ip.device_id = d.id AND ip.main = true')
		    	->where(['and', ['like', 'upper(d.name)', strtoupper($q) . '%', false], ['is not', 'status', null]])
		    	->orderBy('d.name');
		    	
		    	if(!empty($type_id)) {
		    	    $query->andWhere(['d.type_id' => $type_id]);
		    	    
		    	    if (in_array(2, $type_id))
		    	        $query->andWhere(['d.distribution' => false]);
		    	}
		    	
    	        $command = $query->createCommand();
    	        $data = $command->queryAll();
    	        $out['results'] = array_values($data);
            } elseif($id > 0) {
                
                $query = new Query();
                $query->select(['d.id', new \yii\db\Expression("CONCAT(d.name, ' - ', '[', ip, ']', ' - ', m.name)")])
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
    }
    
	public function actionListFromStore($q = null, $type = null) {
	
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		 
		$out = ['results' => ['id' => '', 'concat' => '']];
		 
		if (!is_null($q)) {
	
			$query = new Query();
			$query->select(['d.id', new \yii\db\Expression("
	    		CONCAT(m.name, chr(9), d.mac, ' - ', d.serial)
	    	")])
	    	->from('device d')
	    	->join('INNER JOIN', 'model m', 'm.id = d.model')
	    	->where(['like', new \yii\db\Expression('CAST(mac AS varchar)'), $q])
	    	->orWhere(['like', 'm.name', $q]);
	
	    	if(!is_null($type))
	    		$query->andWhere(['type' => $type]);
	
    		$command = $query->createCommand();
    		$data = $command->queryAll();
    		$out['results'] = array_values($data);
		}
		elseif ($id > 0) {
			$out['results'] = ['id' => $id, 'concat' => Device::findOne($id)->modelAddress->fullDeviceAddress];
		}
		return $out;
	}

    protected function findModel($id)
    {
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
