<?php

namespace console\controllers;

use yii\console\Controller;
use backend\models\Device;
use backend\models\Ip;

class DeviceController extends Controller {
	
	public function actionList() {
		
		//L3
		$modelsDeviceL3 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and',['is not', 'address', null],['layer3' => true],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDeviceL3 as $modelDeviceL3){
			$ipsList .= $modelDeviceL3->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//L3-172
		$modelsDeviceL3_172 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and',['is not', 'address', null],['layer3' => true],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '172.20.'],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDeviceL3_172 as $modelDeviceL3_172){
			$ipsList .= $modelDeviceL3_172->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//L3-10
		$modelsDeviceL3_10 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and',['and',['is not', 'address', null],['layer3' => true]],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '10.'],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDeviceL3_10 as $modelDeviceL3_10){
			$ipsList .= $modelDeviceL3_10->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x900
		$modelsDevicex900 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x900'],['is not', 'address', null],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex900 as $modelDevicex900){
			$ipsList .= $modelDevicex900->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x900", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x900-172
		$modelsDevicex900_172 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x900'],['is not', 'address', null],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '172.20.'],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex900_172 as $modelDevicex900_172){
			$ipsList .= $modelDevicex900_172->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x900-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x900-10
		$modelsDevicex900_10 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x900'],['is not', 'address', null],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '10.'],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex900_10 as $modelDevicex900_10){
			$ipsList .= $modelDevicex900_10->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x900-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//8000GS
		$modelsDevice8000GS = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', '8000GS'],['is not', 'address', null],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevice8000GS as $modelDevice8000GS){
			$ipsList .= $modelDevice8000GS->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//8000GS-24p
		$modelsDevice8000GS_24 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', '8000GS/24'],['is not', 'address', null],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevice8000GS_24 as $modelDevice8000GS_24){
			$ipsList .= $modelDevice8000GS_24->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS-24", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//8000GS-48p
		$modelsDevice8000GS_48 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', '8000GS/48'],['is not', 'address', null],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevice8000GS_48 as $modelDevice8000GS_48){
			$ipsList .= $modelDevice8000GS_48->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS-48", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x210
		$modelsDevicex210 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x210'],['is not', 'address', null],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex210 as $modelDevicex210){
			$ipsList .= $modelDevicex210->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x210", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x210-172
		$modelsDevicex210_172 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x210'],['is not', 'address', null],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '172.20.'],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex210_172 as $modelDevicex210_172){
			$ipsList .= $modelDevicex210_172->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x210-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x210-10
		$modelsDevicex210_10 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x210'],['is not', 'address', null],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '10.'],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex210_10 as $modelDevicex210_10){
			$ipsList .= $modelDevicex210_10->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x210-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x510
		$modelsDevicex510 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x510'],['is not', 'address', null],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex510 as $modelDevicex510){
			$ipsList .= $modelDevicex510->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x510", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x510-172
		$modelsDevicex510_172 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x510'],['is not', 'address', null],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex510_172 as $modelDevicex510_172){
			$ipsList .= $modelDevicex510_172->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x510-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x510-10
		$modelsDevicex510_10 = Device::find()->joinWith('modelIps')->joinWith('modelModel')->
		where(['and', ['like', 'model.name', 'x510'],['is not', 'address', null],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($modelsDevicex510_10 as $modelDevicex510_10){
			$ipsList .= $modelDevicex510_10->modelIps[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x510-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
	}
	
	public function actionSsh() {
		
		$methods = [
				'kex' => 'diffie-hellman-group1-sha1',
				'client_to_server' => [
						'crypt' => '3des-cbc',
						'comp' => 'none'
				],
				'server_to_client' => [
						'crypt' => 'aes256-cbc,aes192-cbc,aes128-cbc',
						'comp' => 'none'
				]
		];
		
		//echo "Connexion SSH ";
		if (!($connection = ssh2_connect("10.224.2.2", 22222, $methods))) {
			echo "[FAILED]<br />";
			exit(1);
		} else {
			//echo "[OK]<br />";
			if(!ssh2_auth_password($connection, 'ra-daniel', 'Mustang1986.')){
				echo "Nie zalogowano";
				exit(1);
			}
			
			//$stream = ssh2_exec($connection, 'sh ssh');
			//echo $stream;
			$shell = ssh2_shell($connection, 'xterm');
			
			//$cmd = "sh mac address-table | inc 1.0.8";
			//$shell = ssh2_exec($connection, $cmd);
			//fwrite( $shell, "en\n");
			//sleep(1);
			fwrite( $shell, "sh mac address-table");
			sleep(1);
			//fwrite( $shell, "interface port1.0.1\n");
			//sleep(1);
			//fwrite( $shell, "desc ssh2-php-test\n");
			
			//$read = fgets($shell, 4096);
			//echo $read;
			//fclose($shell);
			//stream_set_blocking($shell, true);
			$stream_out = ssh2_fetch_stream($shell, SSH2_STREAM_STDIO);
			echo stream_get_contents($stream_out);
			
			//echo "Output: " . stream_get_contents($shell);
			//$data = "";
			while ($buf = fgets($shell)) {
				flush();
				echo $buf;
			}
			//echo $data;
			
			fclose($shell);
			//echo $data;
		}
	}

	function actionSnmp(){
		
		snmpset(
			"172.20.7.254",
			"1nn3c0mmun1ty",
			['1.3.6.1.4.1.89.87.2.1.3.1', '1.3.6.1.4.1.89.87.2.1.4.1', '1.3.6.1.4.1.89.87.2.1.6.1', '1.3.6.1.4.1.89.87.2.1.8.1', '1.3.6.1.4.1.89.87.2.1.12.1', '1.3.6.1.4.1.89.87.2.1.17.1'],
			['i', 'a', 's', 'i', 'i', 'i'],
			[3, '172.20.4.18', 'test.txt', 1, 2, 4]
		);
		
		sleep(1);
		
		snmpset(
			"172.20.7.254",
			"1nn3c0mmun1ty",
			['1.3.6.1.4.1.89.87.2.1.3.1', '1.3.6.1.4.1.89.87.2.1.7.1', '1.3.6.1.4.1.89.87.2.1.8.1', '1.3.6.1.4.1.89.87.2.1.12.1', '1.3.6.1.4.1.89.87.2.1.17.1'],
			['i', 'i', 'i', 'i', 'i'],
			[1, 2, 1, 3, 4]
		);
	}
	
	public function actionInfo() {
		
		phpinfo();
	}
	
	public function actionApiIcinga() {
		
		//$cmd = <<<EOT
		//curl -k -s -u api:apipass -H 'Accept: application/json' -X PUT 'https://localhost:5665/v1/objects/hosts/test8000v4' -d '{ "templates": [ "generic-host" ], "attrs": { "address": "172.20.7.254", "check_command": "hostalive", "vars.device" : "switch" } }' | python -m json.tool
		//EOT;
		//$out = shell_exec($cmd);
		
		//echo '1';
		
		$data = [
				'templates' => [ 'generic-host' ],
				'attrs' => [
						'address' => '172.20.7.254',
						'check_command' => 'hostalive',
						'vars.device' => 'switch'
				]
		];
		$data_json = json_encode($data);
		
		//echo '2';
		
		$hand = curl_init();
		curl_setopt($hand, CURLOPT_URL, 'https://10.111.233.4:5665/v1/objects/hosts/test8000v5');
		curl_setopt($hand, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($hand, CURLOPT_USERPWD, 'api:apipass');
		curl_setopt($hand, CURLOPT_HTTPHEADER, ['Accept : application/json']);
		curl_setopt($hand, CURLOPT_POSTFIELDS,$data_json);
		curl_setopt($hand, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($hand, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($hand, CURLOPT_SSL_VERIFYHOST, false);
		
		//echo '3';
		
		if (!curl_exec($hand))
			echo curl_error($hand);
		else	
			echo 'TAK';
		
		curl_close($hand);
			
			
			
			
	}
	
}
?>