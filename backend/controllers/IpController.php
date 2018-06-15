<?php

namespace backend\controllers;

use backend\models\Device;
use backend\models\HistoryIpSearch;
use backend\models\Ip;
use backend\models\IpSearch;
use backend\models\Subnet;
use IPBlock;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class IpController extends Controller
{  
	public function actionView($device)
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
	    
	    $request = \Yii::$app->request;
	    $transaction = \Yii::$app->db->beginTransaction();
	    
	    if ($request->isAjax) {
	        
	        if (is_null($deviceId)) 
	            return "Parametr $deviceId jest null'em";
	        
            $device = Device::findOne($deviceId);
            $ips = $device->ips;
	        
            if ($request->post('save')){
                if ($nets = $request->post('network')) {
                    $newIps = [];
                    
                    $index = 0;
                    foreach ($nets as $net) {
                        $newIp = new Ip();
                        $newIp->ip = $net['ip'];
                        $newIp->subnet_id = $net['subnet'];
                        $newIp->device_id = $device->id;
                        $newIp->main = $index == 0 ? true : false;
                        $newIps[] = $newIp;
                        
                        $index++;
                    }
                    
                    try {
                        foreach ($ips as $ip) {
                            $ip->delete();
                        }
                        
                        foreach ($newIps as $newIp) {
                            if (!$newIp->save()) throw new \Exception('Problem z zapisem adresu ip');
                        }
                    } catch (\Throwable $t){
                        $transaction->rollBack();
                        var_dump($t->getMessage());
                        exit();
                    }
                    
                    $transaction->commit();
                    return 1;
                } else {
                    try {
                        foreach ($ips as $ip){
                            $ip->delete();
                        }
                    }
                    catch (\Throwable $t){
                        $transaction->rollBack();
                        var_dump($t->getMessage());
                        exit();
                    }
                    
                    $transaction->commit();
                    return 1;
                }
            } else {
                return $this->renderAjax('update', [
                    'ips' => $ips,
                    'deviceId' => $deviceId
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
				    $select = '';
				    
					if(!ksort($allIps, SORT_NATURAL | SORT_FLAG_CASE))
						return '<option>-</option>';
					
					if(array_count_values($allIps) > 0){
						foreach ($allIps as $allIp){
							$select .= '<option value="' . $allIp . '">' . $allIp . '</option>';
						}
					} else {
						return '<option>-</option>';
					}
					return $select;
				case 'free':
					$useIps = ArrayHelper::map(Ip::find()->where(['subnet_id' => $subnet])->all(), 'ip', 'ip');
					$freeIps = array_diff($allIps, $useIps);
					$select = '';
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
								$select = '<option value="' . $freeIp . '" selected="selected">' . $freeIp . '</option>';
							$select .= '<option value="' . $freeIp . '">' . $freeIp . '</option>';
						}
					} else {
						$select = '<option>-</option>';
					}
					return $select;
				case 'use':
					$useIps = ArrayHelper::map(Ip::find()->where(['subnet' => $subnet])->all(), 'ip', 'ip');
					$select = '';
					if(!ksort($useIps, SORT_NATURAL | SORT_FLAG_CASE))
						return '<option>-</option>';
					
					if(array_count_values($useIps) > 0){
						foreach ($useIps as $useIp){
							$select .= '<option value="' . $useIp . '">' . $useIp . '</option>';
						}
					} else {
						return '<option>-</option>';
					}
					return $select;
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
    
    public function actionIndex($subnetId){
    	
    	$ip = new IpSearch();
		$dataProvider = $ip->search(\Yii::$app->request->queryParams);
		
		$dataProvider->query->joinWith('device')->andWhere(['subnet_id' => $subnetId])->orderBy('ip');
	
		return $this->renderAjax('index', [
			'dataProvider' => $dataProvider,
		]);
    }
    
    public function actionHistory(){
    	
    	$historyIp = new HistoryIpSearch();
    	$dataProvider = $historyIp->search(Yii::$app->request->queryParams);
    	
    	return $this->render('history', [
			'historyIp' => $historyIp,
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
