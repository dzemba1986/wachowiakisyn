<?php

namespace console\controllers;

use yii\console\Controller;
use backend\models\Dhcp;
use backend\models\Subnet;
use backend\models\Ip;
use backend\models\HistoryIp;
use backend\models\Device;

class IpAddressController extends Controller {
	
	// The command "yii example/create test" will call "actionCreate('test')"
	public function actionChangeSubnet($old, $new) {
		
		if (!is_object($oldSubnet = Subnet::findOne(['ip' => $old]))){
			echo "Brak posieci $old";
			exit();
		}
		
		$idOldSubnet = $oldSubnet->id; 
		
		$oldBlockIp = Ip::find()->where(['subnet' => $idOldSubnet])->orderBy('ip')->all();
		$newBlockIp = new \IPv4Block($new);
		
		if (count($oldBlockIp) > (int) $newBlockIp->getNbAddresses()){
			echo 'Nowa podsieć jest za mała !!!'; 
			exit();
		}
		
		//var_dump($idOldSubnet); exit();
		$i = 1;
		
		foreach ($oldBlockIp as $oldIp){
			
			if ($i <> 1){
				$modelHistoryIp = HistoryIp::findOne(['ip' => $oldIp->ip, 'address' => Device::findOne($oldIp->device)->address, 'to_date' => null]);
// 				var_dump($oldIp->ip); exit();
				$modelHistoryIp->to_date = date('Y-m-d H:i:s');
			
				try {
					if(!($modelHistoryIp->save()))
						throw new Exception('Problem z zapisem histori ip');
				} catch (Exception $e) {
					var_dump($modelHistoryIp->errors);
					exit();
				}
			}
			
			$oldIp->ip  = $newBlockIp[$i];
			if($oldIp->save())
				echo "zapisano ip $oldIp->ip\n";
			
			if ($i <> 1){
				$modelHistoryIp = new HistoryIp();
				
				$modelHistoryIp->scenario = HistoryIp::SCENARIO_CREATE;
				$modelHistoryIp->ip = $oldIp->ip;
				$modelHistoryIp->from_date = date('Y-m-d H:i:s');
				$modelHistoryIp->address = Device::findOne($oldIp->device)->address;
				
				if(!($modelHistoryIp->save()))
					throw new Exception('Problem z zapisem histori ip');
			}
			
			$i++;
		}
		
		$oldSubnet->ip = $new;
		$oldSubnet->save();
// 		$oldBlockIp = new \IPv4Block($oldSubnet);
		
// 		foreach ($oldBlockIp as $oldIp)
// 			echo $oldIp . "\n";

		echo 'koniec ;)';
	}

	// The command "yii example/index city" will call "actionIndex('city', 'name')"
	// The command "yii example/index city id" will call "actionIndex('city', 'id')"
	//public function actionIndex($category, $order = 'name') { ... }

	// The command "yii example/add test" will call "actionAdd(['test'])"
	// The command "yii example/add test1,test2" will call "actionAdd(['test1', 'test2'])"
	//public function actionAdd(array $name) { ... }
}
?>