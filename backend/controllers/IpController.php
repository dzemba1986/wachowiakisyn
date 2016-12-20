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
	
	public function actionUpdateByDevice($device)
	{
		$modelIps = !is_null($device) ? Device::findOne($device)->modelIps : [];
		
		$request = Yii::$app->request;
		
		if ($request->isAjax){
			if(!empty($request->post('network')) && $request->post('save')){
				$newModelIps = [];
				$index = 0;
				foreach ($request->post('network') as $net){
					$newModelIp = new Ip();
					$newModelIp->ip = $net['ip'];
					$newModelIp->subnet = $net['subnet'];
					$newModelIp->main = $index == 0 ? true : false;
					$newModelIp->device = $device;
					$index++;
					 
					$newModelIps[] = $newModelIp;
				}
				$i = 0;
		
				if(count($modelIps) == count($newModelIps)){
					for($i=0; $i <= count($modelIps)-1; $i++){
		
// 						echo 'Tyle samo'; exit();
						//var_dump($modelIps[$i]->ip); exit();
		
						if($modelIps[$i]->ip == $newModelIps[$i]->ip){
							null; //brak zmian
						} else {
							$modelIps[$i]->ip = $newModelIps[$i]->ip;
							$modelIps[$i]->subnet = $newModelIps[$i]->subnet;
							$modelIps[$i]->main = $i == 0 ? true : false;
								
							try {
								if(!$modelIps[$i]->save())
									throw new Exception('Problem z zapisem adresu ip');
							} catch (Exception $e) {
								var_dump($modelIps[$i]->errors);
								var_dump($e->getMessage());
								exit();
							}
						}
					}
					if (Device::findOne($device)->type == 5)
						Dhcp::generateFile(Device::findOne($device)->modelIps[0]->subnet);
					
					return 1;
				} elseif (count($modelIps) > count($newModelIps)){
					 
// 					echo 'Mniej'; exit();
					//var_dump($newModelIps); var_dump($modelIps); exit();
					 
					for($i=0; $i <= count($modelIps)-1; $i++){
						 
						if($i <= count($newModelIps)-1){
							if($modelIps[$i]->ip == $newModelIps[$i]->ip){
								null; //brak zmian
							} else {
								$modelIps[$i]->ip = $newModelIps[$i]->ip;
								$modelIps[$i]->subnet = $newModelIps[$i]->subnet;
								$modelIps[$i]->main = $i == 0 ? true : false;
								 
								try {
									if(!$modelIps[$i]->save())
										throw new Exception('Problem z zapisem adresu ip');
								} catch (Exception $e) {
									var_dump($modelIps[$i]->errors);
									var_dump($e->getMessage());
									exit();
								}
							}
						} else {
							$modelIps[$i]->delete();
						}
					}
					if (Device::findOne($device)->type == 5)
						Dhcp::generateFile(Device::findOne($device)->modelIps[0]->subnet);
					
					return 1;
				} elseif (count($modelIps) < count($newModelIps)){
					 
// 					echo 'Wiecej'; exit();
					//var_dump($newModelIps); var_dump($modelIps); exit();
					foreach ($modelIps as $modelIp)
						$modelIp->delete();
					 
					foreach ($newModelIps as $newModelIp){
								 
						try {
							if(!$newModelIp->save())
								throw new Exception('Problem z zapisem adresu ip');
						} catch (Exception $e) {
							var_dump($newModelIp->errors);
							var_dump($e->getMessage());
							exit();
						}
					}
				}
				if (Device::findOne($device)->type == 5)
					Dhcp::generateFile(Device::findOne($device)->modelIps[0]->subnet);
				
				return 1;
				//var_dump($newModelIps); exit();
			} elseif ($request->post('save')){
// 				echo 'Usuń wszystko'; exit();
				
				foreach ($modelIps as $modelIp)
					$modelIp->delete();
				
				return 1;
			} else {
				return $this->renderAjax('update_by_device', [
					'modelIps' => $modelIps,
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
			$dataProvider->query->andWhere(['subnet' => $subnet])->orderBy('ip');
	
		return $this->renderPartial('grid', [
			'modelSubnet' => $modelIp,
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
