<?php

namespace backend\controllers;

use Yii;
use backend\models\Tree;
use backend\models\Model;
use backend\models\Address;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\models\Device;
use yii\base\Exception;
use backend\models\Connection;
use backend\models\Host;
use backend\models\Ip;
use yii\db\Query;
use backend\models\Dhcp;

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
        ];
    }

    /**
     * Lists all Modyfication models.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        return $this->render('index', [
        	'id' => $id	
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Tree::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetChildren($id) {
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        	$children = Tree::find()->
        	joinWith('modelDevice')->
        	where(['parent_device' => $id])->
        	orderBy('parent_port')->all();
        	
        	 
        	$arChildren = [];
        	
        	foreach ($children as $child){
        	
        		$model = Model::findOne(Device::findOne($id)->model);
        		
//         		var_dump($child); exit();
//         		$address = Address::findOne($child->modelDevice->address);
        	
        		$arChildren[] = [
        			'id' => (int) $child->modelDevice->id . '.' . $child->port,
        			'text' => $id != 1	?	
        				$model->port[$child->parent_port].'	:<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $child->modelDevice->modelType->icon .'\'); background-position: center center; background-size: auto auto;"></i>'.$child->modelDevice->name  :	 
        				'<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $child->modelDevice->modelType->icon .'\'); background-position: center center; background-size: auto auto;"></i>'.$child->modelDevice->name,
        			'state' => $child->modelDevice->model == 5 ? ['opened' => true] : [], //dla centralnych automatyczne rozwijanie
        			'icon' => false,
        			'port' => $child->port,
        			'parent_port' => $model->port[$child->parent_port],	
        			'children' => $child->modelDevice->modelType->children,
        		];
        	}
        	
        	return $arChildren;
    }
    
    public function actionSearch($str) {
    
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	
    	if (strlen($str) > 3){
	    	$path = [];
	    	
	    	$modelsDevice = Device::find()->where(['like', 'name', $str])->andWhere(['status' => true])->all();
	    	
	    	foreach ($modelsDevice as $modelDevice) {
	    		
	    		$modelTree = Tree::findOne($modelDevice->id);
	    		$id = $modelTree->device;
	    		$parent = $modelTree->parent_device;
	    		
	    		while ($parent <> 3) {
	    		
	    			$modelTree = Tree::findOne($id);
	    			$parent = $modelTree->parent_device;
	    			$modelTree = Tree::find()->where(['device' => $parent])->one();
	    			
	    			if (!in_array($modelTree->device . '.' . $modelTree->port, $path))
	    				array_push($path, $modelTree->device . '.' . $modelTree->port);
	    		
	    			$id = $parent;
	    		}
	    	}
// 	    	if (is_object($modelTree)){
// 		    	$parent = $modelTree->parent_device;
		    	
		    	
// 	    	} else 
// 	    		return null;
    	} else 
    		return null;
//     	$children = Tree::find()->
//     	joinWith('modelDevice')->
//     	where(['device' => $str]);
    	 
    
//     	$arChildren = [];
    	 
//     	foreach ($children as $child){
    		 
//     		$model = Model::findOne(Device::findOne($id)->model);
//     		$address = Address::findOne($child->modelDevice->address);
    		 
//     		$arChildren[] = [
//     				'id' => (int) $child->modelDevice->id . '.' . $child->port . '-' . rand(1,1000),
//     				'text' => $id != 1	?
//     				$model->port[$child->parent_port - 1].'	:<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $child->modelDevice->modelType->icon .'\'); background-position: center center; background-size: auto auto;"></i>'.$address->fullDeviceAddress  :
//     				'<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $child->modelDevice->modelType->icon .'\'); background-position: center center; background-size: auto auto;"></i>'.$address->fullDeviceAddress,
//     				'state' => $child->modelDevice->model == 5 ? ['opened' => true] : [], //dla centralnych automatyczne rozwijanie
//     				'icon' => false,
//     				'port' => $child->port,
//     				'parent_port' => $model->port[$child->parent_port - 1],
//     				'children' => $child->modelDevice->modelType->name <> 'Host' ? TRUE : FALSE,
//     		];
//     	}
    	 
    	return array_reverse($path);
//     	return ["3.74","10.12","5442.47"];
    }
    
    public function actionAdd($id, $host = false)
    { 	
    	$request = Yii::$app->request;
    	
    	if($request->isAjax){
	    	if (!$host){
	    		
		    	$modelDevice = Device::findOne($id);
		    	$modelTree = new Tree();
		    	$modelAddress = new Address();
		    	
		    	//var_dump($modelTree); exit();
		    
		    	if ($modelTree->load($request->post()) && $modelAddress->load($request->post())) {
		    
		    		$modelTree->device = $id;
		    		$modelDevice->status = true;
		    		
		    		$transaction = Yii::$app->getDb()->beginTransaction();    		
		    		
		    		try {
		    			if (!$modelAddress->save())
		    				throw new Exception('Problem z zapisem adresu');
		    			if (!$modelTree->save())
		    				throw new Exception('Problem z zapisem drzewa');
		
		    			$modelDevice->address = $modelAddress->id;
		    			$modelDevice->original_name = true;
		    			$modelDevice->name = $modelDevice->modelAddress->fullDeviceShortAddress;
		    			
		    			if (!$modelDevice->save())
		    				throw new Exception('Problem z zapisem device');
		    			
		    			$transaction->commit();    			
		    			$this->redirect(['tree/index']);
		    		} catch (\Exception $e) {
		    			$transaction->rollBack();
		    			echo $e->getMessage();
		    		}	
		    	} else {
		    		
		    		return $this->renderAjax('add', [
		    				'modelDevice' => $modelDevice,
		    				'modelTree' => $modelTree,
		    				'modelAddress' => $modelAddress,
		    		]);
		    	}
	    	} else {
    		
	    		$modelIp = new Ip();
	    		$modelTree = new Tree();
	    		 
    			if($request->post('ip')){
    				$transaction = Yii::$app->getDb()->beginTransaction();
    				 
    				$modelConnection = Connection::findOne($id);
    				 
    				$modelDevice = new Host();
    				$modelDevice->status = true;
    				$modelDevice->mac = $modelConnection->mac;
    				$modelDevice->address = $modelConnection->address;
    				$modelDevice->start_date = date('Y-m-d H:i:s');
    				 
    				try {
    					if (!$modelDevice->save())
    						throw new Exception('Problem z zapisem device');
    				} catch (\Exception $e) {
    					$transaction->rollBack();
    					return $modelDevice; //$e->getMessage();
    				}
    				
    				$modelDevice->original_name = true;
    				$modelDevice->name = $modelDevice->modelAddress->fullDeviceShortAddress;
    				$modelDevice->save();
    				
    				$modelTree->device = $modelDevice->id;
    				$modelTree->port = 0;
    				$modelTree->parent_device = $modelConnection->device;
    				$modelTree->parent_port = $modelConnection->port;
    				 
    				try {
    					if (!$modelTree->save())
    						throw new Exception('Problem z zapisem na drzewie');
    				} catch (\Exception $e) {
    					$transaction->rollBack();
    					return $e->getMessage();
    				}
    				 
    				$modelIp->ip = $request->post('ip');
    				$modelIp->subnet = $request->post('subnet');
    				$modelIp->main = true;
    				$modelIp->device = $modelDevice->id;
    				 
    				try {
    					if (!$modelIp->save())
    						throw new Exception('Problem z zapisem ip');
    				} catch (\Exception $e) {
    					$transaction->rollBack();
    					return $e->getMessage();
    				}
    				 
    				$modelConnection->host = $modelDevice->id;
    				$modelConnection->conf_date = date('Y-m-d');
    				$modelConnection->conf_user = Yii::$app->user->identity->id;
    		
    				try {
    					if (!$modelConnection->save())
    						throw new Exception('Problem z zapisem połączenia');
    				} catch (\Exception $e) {
    					$transaction->rollBack();
    					return $e->getMessage();
    				}
    				 
    				$transaction->commit();
    				
    				$this->redirect(['tree/index']);
    				
    				Dhcp::generateFile($request->post('subnet'));
    			} else {
    		
    				return $this->renderAjax('add_host_network', [
    						'modelIp' => $modelIp,
    				]);
    			}
    		}
    	} else
    		echo 'Zapytanie nie ajaxowe';
	}
    
//     public function actionAddHost($id){
    	
//     	$modelIp = new Ip();
//     	$modelTree = new Tree();
    	
//     	$request = Yii::$app->request;
    	
//     	if($request->isAjax){
//     		if($request->post('ip')){
//     			$transaction = Yii::$app->getDb()->beginTransaction();
    			
//     			$modelConnection = Connection::findOne($id);
    			
//     			$modelDevice = new Host();
//     			$modelDevice->status = true;
//     			$modelDevice->mac = $modelConnection->mac;
//     			$modelDevice->address = $modelConnection->address;
    			
//     			try {
//     				if (!$modelDevice->save())
//     					throw new Exception('Problem z zapisem device');
//     			} catch (\Exception $e) {
//     				$transaction->rollBack();
//     				return $e->getMessage();
//     			}
    			
//     			$modelTree->device = $modelDevice->id;
//     			$modelTree->port = 1;
//     			$modelTree->parent_device = $modelConnection->device;
//     			$modelTree->parent_port = $modelConnection->port;
    			
//     			try {
//     				if (!$modelTree->save())
//     					throw new Exception('Problem z zapisem na drzewie');
//     			} catch (\Exception $e) {
//     				$transaction->rollBack();
//     				return $e->getMessage();
//     			}
    			
//     			$modelIp->ip = $request->post('ip');
//     			$modelIp->subnet = $request->post('subnet');
//     			$modelIp->main = true;
//     			$modelIp->device = $modelDevice->id;
    			
//     			try {
//     				if (!$modelIp->save())
//     					throw new Exception('Problem z zapisem ip');
//     			} catch (\Exception $e) {
//     				$transaction->rollBack();
//     				return $e->getMessage();
//     			}
    			
//     			$modelConnection->host = $modelDevice->id;
//     			$modelConnection->conf_date = date('Y-m-d');
//     			$modelConnection->conf_user = Yii::$app->user->identity->id;
    			 
//     			try {
//     				if (!$modelConnection->save())
//     					throw new Exception('Problem z zapisem połączenia');
//     			} catch (\Exception $e) {
//     				$transaction->rollBack();
//     				return $e->getMessage();
//     			}
    			
//     			$transaction->commit();
//     			$this->redirect(['tree/index']);
//     		} else {
    		
// 	    		return $this->renderAjax('add_host_network', [
// 	   				'modelIp' => $modelIp,
// 	    		]);
//     		}
//     	} 
//     }
    
    public function actionSelectListPort($device, $mode='free', $type = null)
    {
    	$model = Model::findOne(Device::findOne($device)->model);
    	 
    	switch ($mode){
    		case 'free' :
    			$modelsTree = Tree::find()->select('parent_port')->where(['parent_device' => $device])
    				->union(Tree::find()->select('port AS parent_port')->where(['device' => $device]))->all();
    			
    			$ports_count = Tree::find()->select('parent_port')->where(['parent_device' => $device])
    				->union(Tree::find()->select('port AS parent_port')->where(['device' => $device]))->count();
    			
    				
    			if ($ports_count > 0){
    				foreach ($modelsTree as $modelTree){
    					$ports[$modelTree->parent_port] = $modelTree->parent_port;
    				}
    				
    				$free_ports = array_diff_key($model->port, $ports);
    				//var_dump($ports); var_dump($model->port); exit();
    				if (!$type == 'SEU'){
    					echo '<option value="-1">Brak miejsca</option>';
    					echo '<option value="-2">Brak na liście</option>';
    				}
    				foreach ($free_ports as $key => $free_port ){
    					echo '<option value="' . ($key) . '">' . $free_port . '</option>';
    				}
    			} else {
   					echo '<option value="-1">Brak miejsca</option>';
    				echo '<option value="-2">Brak na liście</option>';
    			}	
    			break;
    			
    		case 'all' :
    			echo '<option>-</option>';
    			foreach ($model->port as $key => $port){
    				echo '<option value="' . ($key) . '">' . $port . '</option>';
    			}
    			break;
    			
    		case 'use' :
    			$modelsTree = Tree::find()->select('parent_port')->where(['parent_device' => $device])
    			->union(Tree::find()->select('port AS parent_port')->where(['device' => $device]))->all();
    			 
    			$ports_count = Tree::find()->select('parent_port')->where(['parent_device' => $device])
    			->union(Tree::find()->select('port AS parent_port')->where(['device' => $device]))->count();
    			 
    			if ($ports_count > 0){
    				foreach ($modelsTree as $modelTree){
    					$ports[$modelTree->parent_port] = $modelTree->parent_port;
    				}
    				
    				echo '<option>-</option>';
    				foreach ($ports as $key => $port ){
    					echo '<option value="' . ($key) . '">' . $port . '</option>';
    				}
    			} else
    				echo '<option>-</option>';
    			break;
    	}
    }
    
//     public function actionFreePortList($id)
//     {	
//     	$query2 = Tree::find()->select('port AS parent_port')->where(['device' => $id]);
//     	$modelsTree = Tree::find()->select('parent_port')->where(['parent_device' => $id])->union($query2)->all();
    	
//     	$ports_count = Tree::find()->select('parent_port')->where(['parent_device' => $id])->union($query2)->count();
		
//     	$model = Model::findOne(Device::findOne($id)->model);
    	
//     	if ($ports_count > 0){
//     		foreach ($modelsTree as $modelTree){
//     			$ports[$modelTree->parent_port - 1] = $modelTree->parent_port;
//     		}
    		
//     		$free_ports = array_diff_key($model->port, $ports);
    		
//     		foreach ($free_ports as $key => $free_port ){
//     			echo '<option value="' . ($key + 1) . '">' . $free_port . '</option>';
//     		}
    		
//     	} else {    		
//     		foreach ($model->port as $key => $port){
//     			echo '<option value="' . ($key + 1) . '">' . $port . '</option>';
//     		}
//     	}
//     }
    
    public function actionMove() {
    	
    	$request = Yii::$app->request;
    	
    	if($request->isAjax){
    		if($request->post()){
    			
    			$device = (int)$request->post('device');
    			$port = $request->post('port');
    			
    			$modelTree = Tree::find()->where(['device' => $device, 'port' => $port])->one();
    			
    			$modelTree->parent_device = (int)$request->post('newParentDevice');
    			$modelTree->parent_port = $request->post('newParentPort');
    			
    			if($modelTree->save()){
    				return 1;
    			} else {
    				return 0;
    			}
     		}
    			
    	}
    }
    
    public function actionCopy() {
    	 
    	$request = Yii::$app->request;
    	 
    	if($request->isAjax){
    		if($request->post()){
    			 
    			$modelTree = new Tree();
    			
    			$modelTree->device = (int)$request->post('device');
    			$modelTree->port = $request->post('port');
    			$modelTree->parent_device = (int)$request->post('newParentDevice');
    			$modelTree->parent_port = $request->post('newParentPort');
    			
    			if($modelTree->save()){
    				return 1;
    			} else {
    				return 0;
    			}
    		}
    		 
    	}
    }
    
    public function actionToStore($id, $port){
    	
    	$modelTree = Tree::findOne(['device' => $id, 'port' => $port]);
    	$modelDevice = Device::findOne($id);
    	
    	$countParent = Tree::find()->where(['parent_device' => $id])->count();
    	$countDevice = Tree::find()->where(['device' => $id])->count();
    	
    	// jeżeli urządzenie nie jest rodzicem
    	if ($countParent == 0) {

    		// jeżeli urządzenie jest ostatnią kopią na drzewie
    		if ($countDevice == 1){
    			
    			try {
    				$modelDevice->address = null;
    				$modelDevice->status = null;
    				
    				if (!$modelDevice->save())
    					throw new Exception('Nie można zapisać do device');
    				
    				foreach ($modelDevice->ips as $modelIp){
    					if (!$modelIp->delete())
    						throw new Exception('Nie można usunąć ip');
    				}
    					
    				if (!$modelTree->delete())
    					throw new Exception('Nie można usunąć agregacji');
    				return 1;
    			} catch (Exception $e) {
    				var_dump($modelDevice->errors);
    				exit();
    			}
    		} else { //nie jest ostatnią kopią
    			try {
    				if (!$modelTree->delete())
    					throw new Exception('Nie można usunąć agregacji');
    				return 1;
    			} catch (Exception $e) {
    				var_dump($e->getMessage());
    				exit();
    			}
    		}
    	} else { //urzadzenie jest rodzicem
    		return null;
    	}
    }
    
    public function actionReplaceFromStore($device)
    {
    	$request = Yii::$app->request;
    	
		if($request->isAjax){
			if($request->post()){
				 
				//var_dump($request->post());
				$transaction = Yii::$app->getDb()->beginTransaction();
				
				foreach ($request->post('map') as $key => $value){
					$modelTree = Tree::find()->where(['parent_device' => $device, 'parent_port' => $key])->one();
					
					if(is_object($modelTree)){
						$modelTree->parent_device = $request->post('deviceDestination');
						$modelTree->parent_port = $value;
						
						try {
							if (!$modelTree->save())
								throw new Exception('Problem z zapisem drzewa');
						} catch (\Exception $e) {
							$transaction->rollBack();
							echo $e->getMessage();
							return 0;
						}
					} else {
						$modelTree = Tree::find()->where(['device' => $device, 'port' => $key])->one();
						$modelTree->device = $request->post('deviceDestination');
						$modelTree->port = $value;
						
						try {
							if (!$modelTree->save())
								throw new Exception('Problem z zapisem drzewa');
						} catch (\Exception $e) {
							$transaction->rollBack();
							echo $e->getMessage();
							return 0;
						}
					}
				}
				
				$deviceSource = Device::findOne($device);
				$deviceDestination = Device::findOne($request->post('deviceDestination'));
				
				$deviceDestination->address = $deviceSource->address;
				$deviceDestination->status = true;
				
				$deviceSource->address = null;
				$deviceSource->status = false;
				
				try {
					if (!$deviceSource->save())
						throw new Exception('Problem z zapisem urządzenia');
					if (!$deviceDestination->save())
						throw new Exception('Problem z zapisem urządzenia');
				} catch (\Exception $e) {
					$transaction->rollBack();
					echo $deviceSource->hasErrors();
					//return 0;
				}
				
				$transaction->commit();
				
				return 1;
			} else {	
	    		return $this->renderAjax('replace_from_store', [
	    			'device' => $device	
	    		]);
			}
    	}
    }
    
    public function actionReplaceDevicePortSelect($deviceSource, $deviceDestination) {
    	
    	$query1 = (Tree::find()->select(['device', 'parent_port AS port'])->where(['parent_device' => $deviceSource]));
    	$query2 = (Tree::find()->select(['parent_device AS device', 'port'])->where(['device' => $deviceSource]));
    	 
    	$modelsTree =  (new \yii\db\Query())
    	->from(['result' => $query1->union($query2)])
    	->orderBy(['port' => SORT_DESC])->all();
    	
    	return $this->renderAjax('replace_device_port', [
    		'modelsTree' => $modelsTree,	
    		'deviceSource' => $deviceSource,
    		'deviceDestination' => $deviceDestination	
    	]);
    }
    
    public function actionPortSelect($mode) {
    	
    	if ($mode == 'move')
    		return $this->renderAjax('port_select_move');
    	elseif ($mode == 'copy')
    		return $this->renderAjax('port_select_copy');
    }
}

