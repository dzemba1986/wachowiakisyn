<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\Subnet;
use backend\models\Ip;
use IPBlock;
use yii\helpers\ArrayHelper;
use backend\models\IpSearch;
use backend\models\Device;
use backend\models\Dhcp;
use backend\models\HistoryIp;
use backend\models\HistoryIpSearch;

class IpController extends Controller
{  
	public function actionViewByDevice($device)
	{
		$modelIps = !is_null($device) ? Device::findOne($device)->modelIps : [];
		
		if(!empty($modelIps)){
			return $this->renderPartial('view', [
					'modelIps' => $modelIps,
			]);
		} else 
			return null;
	}
	
	public function actionUpdate($deviceId) {
	    
	    $request = Yii::$app->request;
	    
	    if ($request->isAjax) {
	        
	        $ips = [];
	        
	        if (!is_null($deviceId)){
	            $device = Device::findOne($deviceId);
	            $ips = $device->ips;
	            if($device->type_id == 5 && isset($device->ips[0])){
	                $oldSubnetId = $device->ips[0]->subnet;
	            }
	        }
	        
	        if ($request->post('save')) {
	            if ($nets = $request->post('network')) {
	                $newIps = [];
	                $index = 0;
	                foreach ($nets as $net) {
	                    $newIp = new Ip();
	                    $newIp->ip = $net['ip'];
	                    $newIp->subnet = $net['subnet'];
	                    $newIp->main = $index == 0 ? true : false;
	                    $newIp->device = $device->id;
	                    
	                    $newIps[] = $newIp;
	                    $index++;
	                }
	                
	                foreach ($ips as $ip) {
	                    if ($device->type_id == 5) {
	                        
	                        $historyIp = HistoryIp::findOne(['ip' => $ip->ip, 'address' => $device->address_id, 'to_date' => null]);
	                        $historyIp->to_date = date('Y-m-d H:i:s');
	                        
	                        try {
	                            if(!($historyIp->save()))
	                                throw new \Exception('Problem z zapisem histori ip');
	                        } catch (\Exception $e) {
	                            var_dump($historyIp->errors);
	                            exit();
	                        }
	                    }
	                    
	                    $ip->delete();
	                }
	                
	                //var_dump($newIps); exit();
	                
	                foreach ($newIps as $newIp) {
	                    try {
	                        if(!$newIp->save())
	                            throw new \Exception('Problem z zapisem adresu ip');
	                        
                            if ($device->type_id == 5){
                                
                                $historyIp = new HistoryIp();
                                
                                $historyIp->scenario = HistoryIp::SCENARIO_CREATE;
                                $historyIp->ip = $newIp->ip;
                                $historyIp->from_date = date('Y-m-d H:i:s');
                                $historyIp->address = $device->address_id;
                                
                                if(!($historyIp->save()))
                                    throw new \Exception('Problem z zapisem histori ip');
                            }
	                    } catch (\Exception $e) {
	                        var_dump($newIp->errors);
	                        var_dump($historyIp->errors);
	                        var_dump($e->getMessage());
	                        exit();
	                    }
	                }
	                
	                if ($device->type_id == 5 && isset($oldSubnetId)){
	                    Dhcp::generateFile([$oldSubnetId, $device->ips[0]->subnet]);
	                }
	                
	                return 1;
	            } else {   //TODO nie tworzy się historia gdy usuwamy kompletnie ip
	                foreach ($ips as $ip)
	                    $ip->delete();
	                    
                    if ($device->type_id == 5 && isset($oldSubnetId)){
                        Dhcp::generateFile([$oldSubnetId, $device->ips[0]->subnet]);
                    }
	                    
                    return 1;
	            }
	        } else {
    	        return $this->renderAjax('update', [
    	            'ips' => $ips,
    	        ]);
	        }
	    }
	}
	
	public function actionSelectList($subnet, $ip = null, $mode = 'all')
	{
		if(is_numeric($subnet)){
			 
			$modelSubnet = Subnet::findOne($subnet);
	
			$blockIp = IPBlock::create($modelSubnet->ip);
			
			//ip block for subnet
			foreach ($blockIp as $objIp){
				$allIps[$objIp->humanReadable(true)] = $objIp->humanReadable(true);
			}
			//delete first and last ip
			$allIps = array_diff($allIps, [$blockIp->getFirstIp()->humanReadable(true), $blockIp->getLastIp()->humanReadable(true)]);
			
			switch ($mode){
				case 'all':
					if(!ksort($allIps, SORT_NATURAL | SORT_FLAG_CASE))
						return '<option>-</option>';
					
					if(array_count_values($allIps) > 0){
						foreach ($allIps as $allIp){
							echo '<option value="' . $allIp . '">' . $allIp . '</option>';
						}
					} else {
						echo '<option>-</option>';
					}
					break;
				case 'free':
					$useIps = ArrayHelper::map(Ip::find()->where(['subnet' => $subnet])->all(), 'ip', 'ip');
					$freeIps = array_diff($allIps, $useIps);
					
					if(isset($ip)){
						//$a = [$ip => $ip];
// 						var_dump($ip); exit();
						$freeIps = ArrayHelper::merge([$ip => $ip], $freeIps);
						if(!ksort($freeIps, SORT_NATURAL | SORT_FLAG_CASE)) 
							return '<option>-</option>';
						//var_dump($freeIps); exit();
					}
					
					if(array_count_values($freeIps) > 0){
						foreach ($freeIps as $freeIp){
							if($freeIp == $ip)
								echo '<option value="' . $freeIp . '" selected="selected">' . $freeIp . '</option>';
							echo '<option value="' . $freeIp . '">' . $freeIp . '</option>';
						}
					} else {
						echo '<option>-</option>';
					}
					break;
				case 'use':
					$useIps = ArrayHelper::map(Ip::find()->where(['subnet' => $subnet])->all(), 'ip', 'ip');
					
					if(!ksort($useIps, SORT_NATURAL | SORT_FLAG_CASE))
						return '<option>-</option>';
					
					if(array_count_values($useIps) > 0){
						foreach ($useIps as $useIp){
							echo '<option value="' . $useIp . '">' . $useIp . '</option>';
						}
					} else {
						echo '<option>-</option>';
					}
					break;
			}
		} else {
			echo '<option>-</option>';
		}
	}
	
    public function actionFreeIpBySubnet($subnet)
    {	    	
    	if(is_numeric($subnet)){
    	
	    	$modelSubnet = Subnet::findOne($subnet);
	    	
	    	$blockIps = IPBlock::create($modelSubnet->ip);
	    	foreach ($blockIps as $blockIp){
	     		$allIps[] = (string) $blockIp;
	    	}
	    	
	        $useIps = ArrayHelper::map(Ip::find()->where(['subnet' => $subnet])->all(), 'ip', 'ip');
	        
	        $freeIPs = array_diff($allIps, $useIps);
	        
	        if(array_count_values($freeIPs) > 0){
	            foreach ($freeIPs as $freeIP){
	                echo '<option value="' . $freeIP . '">' . $freeIP . '</option>';
	            }
	        } else {
	            echo '<option>-</option>';
	        }
    	} else {
    		echo '<option>-</option>';
    	}
    }
    
    public function actionListBySubnet($subnet)
    {
    	var_dump($subnet);
    	exit();
    	
    	if($subnet){
	    	$countIps = Ip::find()->where(['subnet' => $subnet])->count();
	    	$ips = Ip::find()->where(['subnet' => $subnet])->all();
	    
	    	if($countIps > 0){
	    		foreach ($ips as $ip){
	    			echo '<option value="' . $ip->id . '">' . $subnet->ip . ' - ' . $subnet->desc . '</option>';
	    		}
	    	} else {
	    		echo '<option>-</option>';
	    	}
    	} else {
    		echo '<option>-</option>';
    	}
    }
    
    public function actionGrid($subnet = null){
    	
    	$modelIp = new IpSearch();
		$dataProvider = $modelIp->search(\Yii::$app->request->queryParams);
		
		if(!is_null($subnet))
			$dataProvider->query->joinWith('modelDevice')->andWhere(['subnet' => $subnet])->orderBy('ip');
	
		return $this->renderAjax('grid', [
			//'modelIp' => $modelIp,
			'dataProvider' => $dataProvider,
		]);
    }
    
    public function actionHistory(){
    	
    	$searchModel = new HistoryIpSearch();
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    	
    	return $this->render('history', [
    			'searchModel' => $searchModel,
    			'dataProvider' => $dataProvider,
    	]);
    }

    protected function findModel($ip, $subnet)
    {
        if ($model = Subnet::find()->where(['ip' => $ip, 'subnet' => $subnet])->one() !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
