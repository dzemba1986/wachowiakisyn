<?php

namespace console\controllers;

use backend\models\Device;
use yii\console\Controller;
use backend\models\Swith;

class DeviceController extends Controller {
	
	public function actionList() {
		
		//L3
		$l3Devices = Device::find()->joinWith(['ips', 'model'])->
		where(['and',['<>', 'address_id', 1],['layer3' => true],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($l3Devices as $l3Device){
			$ipsList .= $l3Device->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//L3-172
		$l3Devices172 = Device::find()->joinWith(['ips', 'model'])->
		where(['and',['<>', 'address_id', 1],['layer3' => true], ['like', new \yii\db\Expression('CAST(ip AS varchar)'), '172.20.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($l3Devices172 as $l3Device172){
			$ipsList .= $l3Device172->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//L3-10
		$l3Devices10 = Device::find()->joinWith(['ips', 'model'])->
		where(['and',['<>', 'address_id', 1],['layer3' => true],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '10.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($l3Devices10 as $l3Device10){
			$ipsList .= $l3Device10->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x900
		$x900Devices = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x900'],['<>', 'address_id', 1],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x900Devices as $x900Device){
			$ipsList .= $x900Device->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x900", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x900-172
		$x900Devices172 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x900'],['<>', 'address_id', 1],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '172.20.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x900Devices172 as $x900Device172){
			$ipsList .= $x900Device172->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x900-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x900-10
		$x900Devices10 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x900'],['<>', 'address_id', 1],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '10.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x900Devices10 as $x900Device10){
			$ipsList .= $x900Device10->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x900-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//8000GS
		$GSDevices = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', '8000GS'],['<>', 'address_id', 1],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($GSDevices as $GSDevice){
			$ipsList .= $GSDevice->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//8000GS-24p
		$GSDevices24 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', '8000GS/24'],['<>', 'address_id', 1],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($GSDevices24 as $GSDevice24){
			$ipsList .= $GSDevices24->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS-24", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//8000GS-48p
		$GSDevices48 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', '8000GS/48'],['<>', 'address_id', 1],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($GSDevices48 as $GSDevice48){
			$ipsList .= $GSDevices48->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/8000GS-48", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x210
		$x210Devices = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x210'],['<>', 'address_id', 1],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x210Devices as $x210Device){
			$ipsList .= $x210Device->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x210", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x210-172
		$x210Devices172 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x210'], ['is not', 'address_id', null],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '172.20.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x210Devices172 as $x210Device172){
			$ipsList .= $x210Device172->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x210-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x210-10
		$x210Devices10 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x210'], ['<>', 'address_id', 1],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '10.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x210Devices10 as $x210Device10){
			$ipsList .= $x210Device10->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x210-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x230
		$x230Devices = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x230'],['<>', 'address_id', 1],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x230Devices as $x230Device){
			$ipsList .= $x230Device->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x230", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x230-172
		$x230Devices172 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x230'], ['<>', 'address_id', 1],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '172.20.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x230Devices172 as $x230Device172){
			$ipsList .= $x230Device172->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x230-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x230-10
		$x230Devices10 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x230'], ['<>', 'address_id', 1],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '10.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x230Devices10 as $x230Device10){
			$ipsList .= $x230Device10->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x230-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x510
		$x510Devices = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x510'],['<>', 'address_id', 1],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x510Devices as $x510Device){
			$ipsList .= $x510Device->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x510", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x510-172
		$x510Devices172 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x510'],['<>', 'address_id', 1],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '172.20.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x510Devices172 as $x510Device172){
			$ipsList .= $x510Device172->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x510-172", "w");
		fwrite($file, $ipsList);
		fclose($file);
		
		//x510-10
		$x510Devices10 = Device::find()->joinWith(['ips', 'model'])->
		where(['and', ['like', 'model.name', 'x510'],['<>', 'address_id', 1],['like', new \yii\db\Expression('CAST(ip AS varchar)'), '10.%', false],['main' => true]])->orderBy('ip.ip')->all();
		
		$ipsList = '';
		
		foreach ($x510Devices10 as $x510Device10){
			$ipsList .= $x510Device10->ips[0]->ip . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/x510-10", "w");
		fwrite($file, $ipsList);
		fclose($file);
	}
	
	function actionIcingaAdd() {
	    
	    $switches = Swith::find()->joinWith(['mainIp', 'model'])->andWhere(['model.name' => 'AT-8000GS/24'])->orderBy('device.name')->limit(1)->all();
	    
	    foreach ($switches as $switch) {
	        echo \Yii::$app->apiIcingaClient->put('objects/hosts/' . $switch->mixName, [
	            "templates" => [ $switch->model->name ],
	            "attrs" => [
	                "address" => $switch->mainIp->ip,
	                'vars.device' => 'switch',
	                'vars.model' => $switch->model->name,
	                'vars.geolocation' => $switch->geolocation,
	            ]
	        ], [
	            'Content-Type' => 'application/json',
	            'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
	            'Accept' => 'application/json'
	        ])->send()->content;
	    }
	}
	
	function actionIcingaDel() {
	    
        echo \Yii::$app->apiIcingaClient->delete('objects/hosts/swOP5G?cascade=1', null, [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
            'Accept' => 'application/json'
        ])->send()->content;
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
		
		snmp2_set("172.20.7.254", "1nn3c0mmun1ty", "1.3.6.1.4.1.89.87.2.1.3.1 i 1 1.3.6.1.4.1.89.87.2.1.9.1 a 172.20.4.18 1.3.6.1.4.1.89.87.2.1.7.1 i 3 1.3.6.1.4.1.89.87.2.1.8.1 i 3 1.3.6.1.4.1.89.87.2.1.11.1 s 8000gs-testowy 1.3.6.1.4.1.89.87.2.1.17.1 i 4");
		//snmp2_set("172.20.7.254", "1nn3c0mmun1ty", ".1.3.6.1.4.1.89.87.2.1.9.1", "a", "172.20.4.18");
		//snmp2_set("172.20.7.254", "1nn3c0mmun1ty", ".1.3.6.1.4.1.89.87.2.1.7.1", "i", "3");
		//snmp2_set("172.20.7.254", "1nn3c0mmun1ty", ".1.3.6.1.4.1.89.87.2.1.8.1", "i", "3");
		//snmp2_set("172.20.7.254", "1nn3c0mmun1ty", ".1.3.6.1.4.1.89.87.2.1.11.1", "s", "8000gs-testowy");
		//snmp2_set("172.20.7.254", "1nn3c0mmun1ty", ".1.3.6.1.4.1.89.87.2.1.17.1", "i", "4");
		
		//snmpset  -c 1nn3c0mmun1ty -v2c -OvQ $x 1.3.6.1.4.1.89.87.2.1.3.1 i 1  1.3.6.1.4.1.89.87.2.1.9.1 a 172.20.4.18 1.3.6.1.4.1.89.87.2.1.7.1 i 3 1.3.6.1.4.1.89.87.2.1.8.1 i 3   1.3.6.1.4.1.89.87.2.1.11.1 s "8000gs-$x.rtf" 1.3.6.1.4.1.89.87.2.1.17.1 i 4 >> $path_to_logs/log8000GS
	}
	
	public function actionInfo() {
		
		phpinfo();
	}
}
?>