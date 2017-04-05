<?php

namespace console\controllers;

use yii\console\Controller;
use backend\models\Device;
use backend\models\Ip;

class DeviceController extends Controller {
	
	public function actionList() {
		
		//8000GS
		$modelsDevice8000GS = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', '8000GS'],['is not', 'address', null]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevice8000GS as $modelDevice8000GS){
			$ipsList .= $modelDevice8000GS->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
// 		//8000GS-24
// 		$modelsDevice8000GS_24 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
// 		where(['and', ['like', 'model.name', '8000GS/24'],['is not', 'address', null]])->orderBy('ip.ip')->all();
		
// 		$ipsList = '';
		
// 		foreach ($modelsDevice8000GS_24 as $modelDevice8000GS_24){
// 			$ipsList .= $modelDevice8000GS_24->modelIps[0]->ip . "\n";
// 		}
		
// 		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS-24", "w");
// 		fwrite($file, $ipsList);
// 		fclose($file);
		
// 		//8000GS-48
// 		$modelsDevice8000GS_48 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
// 		where(['and', ['like', 'model.name', '8000GS/48'],['is not', 'address', null]])->orderBy('ip.ip')->all();
		
// 		$ipsList = '';
		
// 		foreach ($modelsDevice8000GS_48 as $modelDevice8000GS_48){
// 			$ipsList .= $modelDevice8000GS_48->modelIps[0]->ip . "\n";
// 		}
		
// 		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS-48", "w");
// 		fwrite($file, $ipsList);
// 		fclose($file);
		
		//x900
		$modelsDevicex900 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x900'],['is not', 'address', null]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex900 as $modelDevicex900){
			$ipsList .= $modelDevicex900->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x900", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x210
		$modelsDevicex210 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x210'],['is not', 'address', null]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex210 as $modelDevicex210){
			$ipsList .= $modelDevicex210->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x210", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x510
		$modelsDevicex510 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x510'],['is not', 'address', null]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex510 as $modelDevicex510){
			$ipsList .= $modelDevicex510->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x510", "w");
		fwrite($file, $ipsList);
		fclose($file);
	}
}
?>