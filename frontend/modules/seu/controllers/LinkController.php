<?php

namespace frontend\modules\seu\controllers;

use common\models\seu\Link;
use common\models\seu\Model;
use common\models\seu\devices\Device;
use common\models\seu\network\Ip;
use Yii;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\base\Exception;
use yii\db\Expression;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\validators\IpValidator;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class LinkController extends Controller
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

    public function actionIndex($id = null) {
        
        return $this->render('index', [
        	'id' => $id	
        ]);
    }
    
    public function actionIndex2($id = null) {
        
        return $this->render('index2', [
            'id' => $id
        ]);
    }
    
    public function actionGetChildren($id) {
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $modelId = Device::find()->select('model_id')->where(['id' => $id])->asArray()->one()['model_id'];
        $model = Model::find()->select('port')->where(['id' => $modelId])->one();
        $children = [];
        
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
                'children',
                'technic'
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
                    $model->port[$node['parent_port']] . '	:<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i>'.$node['concat'] :
                    $model->port[$node['parent_port']] . '	:<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i><font color="red">'.$node['concat'].'</font>';
                
            	$children[] = [
            		'id' => (int) $node['device'] . '.' . $node['port'],
            	    'text' => !($id == 1 || $id == 2)	? 
                        $text :
            			'<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i>'.$node['concat'],
            		'name' => $node['concat'],
            	    'network' => [
            	        'mac' => $node['mac'],
            	        'ips' => $ips,
            	    ],
            	    'type' => $node['type_id'],
            	    'controller' => Device::getController($node['type_id'], $node['technic']),
            		'state' => $node['model_id'] == 5 ? ['opened' => true] : [], //dla centralnych automatyczne rozwijanie
            		'icon' => false,
            	    'children' => $node['children']
            	];
            }
        } else $children = [];
        
        return $children;
    }
    
    public function actionGetChildren2($id) {
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $modelId = Device::find()->select('model_id')->where(['id' => $id])->asArray()->one()['model_id'];
        $model = Model::find()->select('port')->where(['id' => $modelId])->one();
        $children = [];
        
        $nodes = (new \yii\db\Query())
        ->select([
            new Expression("CASE WHEN proper_name IS NULL THEN concat(prefix, device.name) ELSE concat(prefix, device.name, '_', proper_name) END"),
            'agregation.device',
            'port',
            'parent_port',
            'model_id',
            'status',
            'device.type_id',
            'icon',
            'children',
            'technic'
        ])
        ->from('device')
        ->leftJoin('agregation', 'device.id = agregation.device')
        ->leftJoin('device_type', 'device_type.id = device.type_id')
        ->where(['parent_device' => $id])
        ->orderBy('parent_port')
        ->all();
        
        if (!empty($nodes)) {
            foreach ($nodes as $node){
                $text = $node['status'] ?
                $model->port[$node['parent_port']] . '	:<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i>'.$node['concat'] :
                $model->port[$node['parent_port']] . '	:<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i><font color="red">'.$node['concat'].'</font>';
                
                $children[] = [
                    'id' => (int) $node['device'] . '.' . $node['port'],
                    'text' => !($id == 1 || $id == 2)	?
                    $text :
                    '<i class="jstree-icon jstree-themeicon jstree-themeicon-custom" role="presentation" style="background-image : url(\''. $node['icon'] .'\'); background-position: center center; background-size: auto auto;"></i>'.$node['concat'],
                    'name' => $node['concat'],
                    'type' => $node['type_id'],
                    'controller' => Device::getController($node['type_id'], $node['technic']),
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
	    	    $devices = Device::find()->select('id')->where(['and', ['"mac"::text' => $str], ['status' => true]])->asArray()->all();
	    	} else {
	    	    $devices = Device::find()->select('id')->where(['or', ['id' => (int) $str], ['like', 'name', strtoupper($str) . '%', false]])->andWhere(['is not', 'status', null])->asArray()->all();
	    	}
	    	
	    	//przejscie przez wszystkie wyszukane obiekty typu device 
	    	foreach ($devices as $device) {
	    		
	    		//powiazany element typu tree
	    		$modelTree = Link::findOne(['device' => $device['id']]);
	    		
	    		//dopóki nie jest rootem
	    		while ($modelTree->parent_device <> 1) {
	    			$modelTree = Link::findOne($modelTree->parent_device);
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
        $out = '';
        
        if ($mode == 'free') {
            $links = Link::find()->select('parent_port')->where(['parent_device' => $deviceId])
            ->union(Link::find()->select('port AS parent_port')->where(['device' => $deviceId]))->all();
            
            if (!empty($links)) {
                $usePorts = [];
                foreach ($links as $link) {
                    $usePorts[$link->parent_port] = $link->parent_port;
                }
                
                $freePorts = array_diff_key($model->port->getValue(), $usePorts);
                
                if ($install){
                    $out .= '<option value="-1">Brak miejsca</option>';
                }
                foreach ($freePorts as $key => $freePort ){
                    if ($selected == $key) {
                        $out .= '<option value="' . ($key) . '" selected="1">' . $freePort . '</option>';
                        continue;
                    }
                    $out .= '<option value="' . ($key) . '">' . $freePort . '</option>';
                }
                
            } else {
                $out = '<option value="-1">Brak miejsca</option>';
            }
        } elseif ($mode == 'all') {
            $out .= '<option></option>';
            foreach ($model->port as $key => $port){
                $out .= '<option value="' . ($key) . '">' . $port . '</option>';
            }
        }
        
        return $out;
    }
    
    public function actionMove($deviceId, $port, $newParentId) {
    	
    	$request = Yii::$app->request;
    	
	    if($request->isPost) {
			$link = Link::find()->where(['device' => $deviceId, 'port' => $port])->one();
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
        
        if($request->isPost){
            $link = new Link();
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
    
    public function actionReplacePort($sId, $dId) {
        
        $sDevice = Device::findOne($sId);
        $dDevice = Device::findOne($dId);
        
        $query1 = (Link::find()->select(['device', 'parent_port AS port'])->where(['parent_device' => $sId]));
        $query2 = (Link::find()->select(['parent_device AS device', 'port'])->where(['device' => $sId]));
        
        $links =  (new \yii\db\Query())
        ->from(['result' => $query1->union($query2)])
        ->orderBy(['port' => SORT_ASC])->all();
        
        return $this->renderAjax('replace_port', [
            'links' => $links,
            'sDevice' => $sDevice,
            'dId' => $dId,
            'onetoone' => $sDevice->model_id == $dDevice->model_id ? true : false
        ]);
    }
    
    protected function findModel($id)
    {
        if (($model = Link::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

