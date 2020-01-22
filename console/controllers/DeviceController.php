<?php

namespace console\controllers;

use common\models\seu\devices\Camera;
use common\models\seu\devices\GatewayVoip;
use common\models\seu\devices\Swith;
use yii\base\Exception;
use yii\console\Controller;


class DeviceController extends Controller {
	
	public function actionList() {
		
		//L3
		$l3s = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
            ->where(['and', ['<>', 'address_id', 1], ['layer3' => true]])->orderBy('ip')->asArray()->all();
		
		$ips = '';
		
		foreach ($l3s as $l3) {
			$ips .= $l3['ip'] . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3", "w");
		fwrite($file, $ips);
		fclose($file);
		
		//L3-172
		$l3s_172= Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
            ->where(['and', ['<>', 'address_id', 1], ['layer3' => true], ['like', '"ip"::text', '172.20.%', false]])->orderBy('ip')->asArray()->all();
		
		$ips = '';
		
		foreach ($l3s_172 as $l3_172) {
			$ips .= $l3_172['ip'] . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3-172", "w");
		fwrite($file, $ips);
		fclose($file);
		
		//L3-10
		$l3s_10 = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
            ->where(['and', ['<>', 'address_id', 1], ['layer3' => true], ['like', '"ip"::text', '10.%', false]])->orderBy('ip')->asArray()->all();
		
		$ips = '';
		
		foreach ($l3s_10 as $l3_10) {
			$ips .= $l3_10['ip'] . "\n";
		}
		
		$file = fopen(\Yii::getAlias('@console') . "/device/lists/L3-10", "w");
		fwrite($file, $ips);
		fclose($file);
		
		//by model [model_name => file_name]
		$models = ['x930' => 'x930', 'x900' => 'x900', 'x610' => 'x610', 'x600' => 'x600', 'x510' => 'x510', 'x230' => 'x230', 'x210' => 'x210', 
		  '8000GS' => '8000GS', '8000GS/24' => '8000GS-24', '8000GS/48' => '8000GS-48', 'EC' => 'ecs', 'S1720' => 'hwS1720', '720' => 'hws'];
		
		foreach ($models as $model_name => $file_name) {
		    //all
		    $devices = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
		    ->where(['and', ['like', 'model.name', $model_name], ['<>', 'address_id', 1]])->orderBy('ip')->asArray()->all();
		    
		    $ips = '';
		    
		    foreach ($devices as $device) {
		        $ips .= $device['ip'] . "\n";
		    }
		    
		    $file = fopen(\Yii::getAlias('@console') . '/device/lists/' . $file_name, "w");
		    fwrite($file, $ips);
		    fclose($file);
		    
		    //172.x.x.x
		    $devices_172 = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
		    ->where(['and', ['like', 'model.name', $model_name], ['<>', 'address_id', 1], ['like', '"ip"::text', '172.20.%', false]])->orderBy('ip')->asArray()->all();
		    
		    $ips = '';
		    
		    foreach ($devices_172 as $device_172) {
		        $ips .= $device_172['ip'] . "\n";
		    }
		    
		    $file = fopen(\Yii::getAlias('@console') . '/device/lists/' . $file_name . '-172', "w");
		    fwrite($file, $ips);
		    fclose($file);
		    
		    //10.x.x.x
		    $devices_10 = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
		    ->where(['and', ['like', 'model.name', $model_name], ['<>', 'address_id', 1], ['like', '"ip"::text', '10.%', false]])->orderBy('ip')->asArray()->all();
		    
		    $ips = '';
		    
		    foreach ($devices_10 as $device_10) {
		        $ips .= $device_10['ip'] . "\n";
		    }
		    
		    $file = fopen(\Yii::getAlias('@console') . '/device/lists/' . $file_name . '-10', "w");
		    fwrite($file, $ips);
		    fclose($file);
		}
	}
	
	function actionSave() {
	    
	    $pathAutoSave = \Yii::getAlias('@console/device/save');
	    $log = '';
	    
	    //8000GS
	    $gss = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
	       ->where(['and', ['config' => 1], ['<>', 'address_id', 1]])->orderBy('ip')->asArray()->all();
	    foreach ($gss as $gs) {
	        if (!snmpset($gs['ip'], '1nn3c0mmun1ty', 
	            ['1.3.6.1.4.1.89.87.2.1.3.1', '1 1.3.6.1.4.1.89.87.2.1.7.1', '1.3.6.1.4.1.89.87.2.1.8.1', '1.3.6.1.4.1.89.87.2.1.12.1', '1.3.6.1.4.1.89.87.2.1.17.1'], 
	            ['i', 'i', 'i', 'i', 'i'], 
	            [1, 2, 1, 3, 4],
	            4000000
            )) $log .= $gs['ip'] . " - Błąd SNMP\n";
            
	    }
	    
	    //X-series
	    $methods = [
	        'hostkey'=>'ssh-rsa',
	        'client_to_server' => [
	            'crypt' => 'aes256-ctr,aes192-ctr,aes128-ctr,aes256-cbc,aes192-cbc,aes128-cbc,3des-cbc,blowfish-cbc',
	            'comp' => 'none'
	        ],
	        'server_to_client' => [
	            'crypt' => 'aes256-ctr,aes192-ctr,aes128-ctr,aes256-cbc,aes192-cbc,aes128-cbc,3des-cbc,blowfish-cbc',
	            'comp' => 'none'
	        ]
        ];
	    //nie uwzględnia PLIX'a
	    $xs = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
	       ->where(['and', ['config' => 2], ['<>', 'address_id', 1], ['<>', 'device.id', 19663]])->orderBy('ip')->asArray()->all();
	    
        foreach ($xs as $x) {
            try {
                $connection = ssh2_connect($x['ip'], 22222, $methods);
                ssh2_auth_password($connection, 'ra-daniel', 'Mustang1986.');
                $shell = ssh2_shell($connection, 'xterm');
                fwrite( $shell, "en" . PHP_EOL);
                sleep(1);
                fwrite( $shell, "cop r s" . PHP_EOL);
                sleep(4);
                fclose($shell);
            } catch (\Throwable $t) {
                $log .= $x['ip'] . ' - ' . $t->getMessage() . "\n";    
            }
        }
        
	    //EC
	    $ecs = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
            ->where(['and', ['config' => 5], ['<>', 'address_id', 1]])->orderBy('ip')->asArray()->all();
	    
	    foreach ($ecs as $ec){
	        $log .= $ec['ip'] . ' - ';
	        if (snmp3_set($ec['ip'], 'julka', 'authPriv', 'MD5', 'k1tk@k1tk@', 'AES', 'p@j@kp@j@k',
	            ['1.3.6.1.4.1.259.10.1.45.1.24.1.1.0', '1.3.6.1.4.1.259.10.1.45.1.24.1.3.0', '1.3.6.1.4.1.259.10.1.45.1.24.1.4.0', '1.3.6.1.4.1.259.10.1.45.1.24.1.8.0'],
	            ['i', 'i', 's', 'i'],
	            [2, 3, 'startup1.cfg', 2],
	            4000000
            )) $log .= $ec['ip'] . " - Błąd SNMP\n";
	    }
	    
	    $fileAutoSave = $pathAutoSave . '/save.log';
	    file_put_contents($fileAutoSave, $log);
	}
	
	function actionBackup() {
	    
	    $pathAutoBackup = \Yii::getAlias('@console/device/backup');
	    $log = '';
	    
	    //8000GS
	    $gss = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
	    ->where(['and', ['config' => 1], ['<>', 'address_id', 1]])->orderBy('ip')->asArray()->all();
	    foreach ($gss as $gs) {
	        try {
    	        if (!snmpset($gs['ip'], '1nn3c0mmun1ty',
    	            ['1.3.6.1.4.1.89.87.2.1.3.1', '1.3.6.1.4.1.89.87.2.1.9.1', '1.3.6.1.4.1.89.87.2.1.7.1', '1.3.6.1.4.1.89.87.2.1.8.1', '1.3.6.1.4.1.89.87.2.1.11.1', '1.3.6.1.4.1.89.87.2.1.17.1'],
    	            ['i', 'a', 'i', 'i', 's', 'i'],
    	            [1, '172.20.4.18', 3, 3, '8000GS_' . $gs['ip'] . '.rtf', 4]
                )) {
                    $log .= $gs['ip'] . " - Błąd SNMP\n";
                }
	        } catch (\Throwable $t) {
	            $log .= $gs['ip'] . " - Błąd SNMP\n";
	            continue;
	        }
	    }
	    
	    //X-series
	    $xs = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
            ->where(['and', ['config' => 2], ['<>', 'address_id', 1], ['like', '"ip"::text', '172.20.%', false]])->orderBy('ip')->asArray()->all();
	    foreach ($xs as $x) {
	        try {
    	        if (!snmpset($x['ip'], '1nn3c0mmun1ty',
    	            ['1.3.6.1.4.1.207.8.4.4.4.600.3.13.1.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.2.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.3.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.5.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.6.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.7.0'],
    	            ['a', 'i', 's', 'i', 's', 's'],
    	            ['172.20.4.18', 1, 'default.cfg', 4, 'xSeries_' . $x['ip'] . '.rtf', 1]
                )) {
                    $log .= $x['ip'] . " - Błąd SNMP\n";
                }
	        } catch (\Throwable $t) {
	            $log .= $x['ip'] . " - Błąd SNMP\n";
	            continue;
	        }
	    }
	    
	    $xs = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
            ->where(['and', ['config' => 2], ['<>', 'address_id', 1], ['like', '"ip"::text', '10.%', false]])->orderBy('ip')->asArray()->all();
	    foreach ($xs as $x) {
	        try {
    	        if (!snmpset($x['ip'], '1nn3c0mmun1ty',
    	            ['1.3.6.1.4.1.207.8.4.4.4.600.3.13.1.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.2.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.3.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.5.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.6.0', '1.3.6.1.4.1.207.8.4.4.4.600.3.7.0'],
    	            ['a', 'i', 's', 'i', 's', 's'],
    	            ['172.20.4.18', 1, 'default.cfg', 4, 'xSeries_' . $x['ip'] . '.rtf', 1]
                )) {
                    $log .= $x['ip'] . " - Błąd SNMP\n";
    	        }
	        } catch (\Throwable $t) {
	            $log .= $x['ip'] . " - Błąd SNMP\n";
	            continue;
	        }
	    }
	    
	    //EC
	    $ecs = Swith::find()->select('device.id, ip, model_id')->joinWith(['mainIp', 'model'])
	    ->where(['and', ['config' => 5], ['<>', 'address_id', 1]])->orderBy('ip')->asArray()->all();
	    
	    foreach ($ecs as $ec){
	        try {
    	        if (!snmp3_set($ec['ip'], 'julka', 'authPriv', 'MD5', 'k1tk@k1tk@', 'AES', 'p@j@kp@j@k',
    	            ['1.3.6.1.4.1.259.10.1.45.1.24.1.1.0', '1.3.6.1.4.1.259.10.1.45.1.24.1.3.0', '1.3.6.1.4.1.259.10.1.45.1.24.1.4.0', '1.3.6.1.4.1.259.10.1.45.1.24.1.20.0', '1.3.6.1.4.1.259.10.1.45.1.24.1.21.0', '1.3.6.1.4.1.259.10.1.45.1.24.1.8.0'],
    	            ['i', 'i', 's', 'i', 'x', 'i'],
    	            [3, 4, 'EC_' . $ec['ip'] . '.rtf', 1, '0A6FE904', 2]
                )) {
                    $log .= $ec['ip'] . " - Błąd SNMP\n";
    	        }
	        } catch (\Throwable $t) {
	            $log .= $ec['ip'] . " - Błąd SNMP\n";
	            continue;
	        }
	    }
	    
	    $fileAutoBackup = $pathAutoBackup . '/backup.log';
	    file_put_contents($fileAutoBackup, $log);
	    
	    exec('mkdir /var/tftp/$(date +%Y-%m-%d)');
	    sleep(2);
	    exec('mv /var/tftp/*.rtf /var/tftp/$(date +%Y-%m-%d)');
	    exec('lftp -e "mirror -R /var/tftp/$(date +%Y-%m-%d)/ /switch/; exit" -p 21 -u backup,HmUlrF5mBTYe7UJAOaB3 10.111.233.2');
	}
	
	function actionCheckInactiveSwitch() {
	    
	    $switches = Swith::find()->where(['and', ['<>', 'address_id', 1], ['distribution' => false]])->asArray()->all();
	    
	    $swi = [];
	    foreach ($switches as $switch) {
	        
	        $allHosts = \Yii::$app->db->createCommand('select * from device
                where type_id = 5 and id in (select device from agregation agr where agr.parent_device=:switchId)')
                ->bindParam(':switchId', $switch['id'])->queryAll();
                
                $inactivehosts = \Yii::$app->db->createCommand('select * from device
                where type_id = 5 and status is false and id in (select device from agregation agr where agr.parent_device=:switchId)')
                ->bindParam(':switchId', $switch['id'])->queryAll();
                
                if (count($allHosts) == count($inactivehosts)) $swi[] = $switch['name'] . '_' . $switch['mac'];
	    }
	    
	    print_r($swi);
	}
	
	function actionIcingaAdd() {
	    
// 	    $switches = Swith::find()->joinWith(['mainIp', 'model'])->andWhere(['and', ['monitoring' => null], ['<>', 'address_id', 1]])->orderBy('device.name')->limit(100)->all();
	    
// 	    foreach ($switches as $switch) {
	    $switch = Camera::findOne(7547);
	        echo $switch->mixName;
	        try {
	            echo \Yii::$app->apiIcingaClient->put('objects/hosts/' . $switch->id, [
	                "templates" => [ $switch->model->name ],
	                "attrs" => [
	                    'display_name' => $switch->mixName,
	                    'address' => $switch->mainIp->ip,
	                    'vars.geolocation' => '52.4314987, 16.9251145',
	                    'vars.device' => 'Switch',
	                    'vars.model' => $switch->model->name,
	                ]
	            ], [
	                'Content-Type' => 'application/json',
	                'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
	                'Accept' => 'application/json'
	            ])->send()->content;
	            
	            $switch->monitoring = true;
	            $switch->geolocation = '52.4314987, 16.9251145';
	            if (!$switch->save()) { throw new Exception('Błąd zapisu urządzenia'); }
	        } catch (\Throwable $t) {
	            echo ' - ' . $t->getMessage();
	            exit();
	        }
	        
	        echo " - OK\n";
	        //sleep(5);
// 	    }
	}
	
	function actionIcingaDel($id) {
	    
        echo \Yii::$app->apiIcingaClient->delete("objects/hosts/$id?cascade=1", null, [
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
			//fwrite( $shell, "sh mac address-table");
			//sleep(1);
			fwrite( $shell, "interface port1.0.1\n");
			sleep(1);
			fwrite( $shell, "desc ssh2-php-test\n");
			
			//$read = fgets($shell, 4096);
			//echo $read;
			//fclose($shell);
			//stream_set_blocking($shell, true);
			//$stream_out = ssh2_fetch_stream($shell, SSH2_STREAM_STDIO);
			//echo stream_get_contents($stream_out);
			
			//echo "Output: " . stream_get_contents($shell);
			//$data = "";
// 			while ($buf = fgets($shell)) {
// 				flush();
// 				echo $buf;
// 			}
			//echo $data;
			
			fclose($shell);
			//echo $data;
		}
	}

	function actionSnmp(){
		
	    $tftpToRunning = snmpset(
	        "172.20.7.254",
	        "1nn3c0mmun1ty",
	        ['1.3.6.1.4.1.89.87.2.1.3.1', '1.3.6.1.4.1.89.87.2.1.4.1', '1.3.6.1.4.1.89.87.2.1.6.1', '1.3.6.1.4.1.89.87.2.1.8.1', '1.3.6.1.4.1.89.87.2.1.12.1', '1.3.6.1.4.1.89.87.2.1.17.1'],
	        ['i', 'a', 's', 'i', 'i', 'i'],
	        [3, '172.20.4.18', 'test.txt', 1, 2, 4]
	        );
	    
	    if ($tftpToRunning) {
	        sleep(1);
	        
	        $runningToStartup = snmpset(
	            "172.20.7.254",
	            "1nn3c0mmun1ty",
	            ['1.3.6.1.4.1.89.87.2.1.3.1', '1.3.6.1.4.1.89.87.2.1.7.1', '1.3.6.1.4.1.89.87.2.1.8.1', '1.3.6.1.4.1.89.87.2.1.12.1', '1.3.6.1.4.1.89.87.2.1.17.1'],
	            ['i', 'i', 'i', 'i', 'i'],
	            [1, 2, 1, 3, 4]
	            );
	        
	        if ($runningToStartup) echo "Zrobione\n";
	        else echo "Błąd\n";
	    } else echo "Błąd\n";
	}
	
	public function actionGenerateConfFile() {
	    
	    $pathConf = \Yii::getAlias('@console/device/conf');
	    
	    $dev8000s = Swith::find()->select('id')->where(['model_id' => [2, 21]])->andWhere(['status' => true])->all();
	    
	    foreach ($dev8000s as $dev8000) {
	        
// 	        $hosts = Host::find()->joinWith('links')->where("id in (select device from agregation where parent_device = {$dev8000->id})")->andWhere(['status' => true])->orderBy('parent_port')->all();
// 	        $cameras = Camera::find()->joinWith('links')->where("id in (select device from agregation where parent_device = {$dev8000->id})")->orderBy('parent_port')->all();
	        $gws = GatewayVoip::find()->joinWith('links')->where("id in (select device from agregation where parent_device = {$dev8000->id})")->orderBy('parent_port')->all();
	        $data = '';
// 	        foreach ($hosts as $host) {
// 	            $port = $host->links[0]->parent_port + 1;
// 	            $data .= "interface ethernet g{$port}\n";
// 	            $data .= "rate-limit 938000\n";
//                 $data .= "traffic-shape 830000 8300000\n";
//                 //$data .= "description {$host->getMixName(false)}\n";
//                 $data .= "exit\n";
// 	        }
	        
// 	        foreach ($cameras as $camera) {
// 	            $port = $camera->links[0]->parent_port + 1;
// 	            $data .= "interface ethernet g{$port}\n";
// 	            $data .= "no service-acl input\n";
// 	            $data .= "exit\n";
// 	            $data .= "no ip access-list cam{$port}\n";
// 	            $data .= "ip access-list cam{$port}\n";
// 	            $data .= "deny-udp any any any 68\n";
// 	            $data .= "permit any {$camera->ips[0]->ip} 0.0.0.0 213.5.208.128 0.0.0.63\n";
// 	            $data .= "permit any {$camera->ips[0]->ip} 0.0.0.0 192.168.5.0 0.0.0.255\n";
// 	            $data .= "permit any {$camera->ips[0]->ip} 0.0.0.0 10.111.0.0 0.0.255.255\n";
// 	            $data .= "permit-udp 0.0.0.0 0.0.0.0 68 any 67\n";
// 	            $data .= "exit\n";
// 	            $data .= "interface ethernet g{$port}\n";
// 	            $data .= "service-acl input cam{$port}\n";
// 	            $data .= "exit\n";
// 	        }
	        
	        foreach ($gws as $gw) {
	            $port = $gw->links[0]->parent_port + 1;
	            
	            $data .= "interface vlan 3\n";
	            $data .= "bridge address {$gw->mac} permanent ethernet g{$port}\n";
	            $data .= "interface ethernet g{$port}\n";
	            $data .= "no service-acl input\n";
	            $data .= "exit\n";
	            $data .= "no ip access-list voip{$port}\n";
	            $data .= "ip access-list voip{$port}\n";
	            $data .= "deny-udp any any any 68\n";
	            $data .= "permit any {$gw->ips[0]->ip} 0.0.0.0 213.5.208.0 0.0.0.63\n";
	            $data .= "permit any {$gw->ips[0]->ip} 0.0.0.0 213.5.208.128 0.0.0.63\n";
	            $data .= "permit any {$gw->ips[0]->ip} 0.0.0.0 10.111.0.0 0.0.255.255\n";
	            $data .= "permit-udp 0.0.0.0 0.0.0.0 68 any 67\n";
	            $data .= "exit\n";
	            $data .= "interface ethernet g{$port}\n";
	            $data .= "shutdown\n";
	            $data .= "switchport trunk allowed vlan remove all\n";
	            $data .= "switchport mode access\n";
	            $data .= "description {$gw->getMixName(false)}\n";
	            $data .= "switchport access vlan 3\n";
	            $data .= "spanning-tree portfast\n";
	            $data .= "spanning-tree bpduguard\n";
	            $data .= "service-acl input voip{$port}\n";
	            $data .= "port security mode lock\n";
	            $data .= "port security discard\n";
	            $data .= "no shutdown\n";
	            $data .= "exit\n";
	        }  
	        
	        $fileConf = $pathConf . '/8000gs/' . $dev8000->ips[0]->ip . '.cfg';
	        file_put_contents($fileConf, $data);
	    }
	    
	    $devXs = Swith::find()->select('id')->where(['model_id' => [47, 60, 46, 72, 73, 74, 58, 59]])->andWhere(['status' => true])->all();
	    
	    foreach ($devXs as $devX) {
	        
// 	        $hosts = Host::find()->joinWith('links')->where("id in (select device from agregation where parent_device = {$devX->id})")->andWhere(['status' => true])->orderBy('parent_port')->all();
	        $gws = GatewayVoip::find()->joinWith('links')->where("id in (select device from agregation where parent_device = {$devX->id})")->orderBy('parent_port')->all();
	        
	        $data = '';
	        $data .= "enable\n";
	        $data .= "configure terminal\n";
	        foreach ($gws as $gw) {
	            $port = $host->links[0]->parent_port + 1;
	            
	            $data .= "interface port1.0.{$port}\n";
	            $data .= "no access-group voip{$port}\n";
	            $data .= "exit\n";
	            $data .= "no access-list hardware voip{$port}\n";
	            $data .= "access-list hardware voip{$port}\n";
	            $data .= "deny udp any any eq 68\n";
	            $data .= "permit ip {$gw->ips[0]->ip} 0.0.0.0 213.5.208.0 0.0.0.63\n";
	            $data .= "permit ip {$gw->ips[0]->ip} 0.0.0.0 213.5.208.128 0.0.0.63\n";
	            $data .= "permit ip {$gw->ips[0]->ip} 0.0.0.0 10.111.0.0 0.0.255.255\n";
	            $data .= "permit udp 0.0.0.0 0.0.0.0 eq 68 any eq 67\n";
	            $data .= "deny ip any any\n";
	            $data .= "exit\n";
	            $data .= "interface port1.0.{$port}\n";
	            $data .= "shutdown\n";
	            $data .= "description {$gw->getMixName(false)}\n";
	            $data .= "switchport access vlan 3\n";
	            $data .= "access-group voip{$port}\n";
	            $data .= "switchport port-security violation protect\n";
	            $data .= "switchport port-security maximum 0\n";
	            $data .= "switchport port-security\n";
	            $data .= "spanning-tree portfast\n";
	            $data .= "spanning-tree portfast bpdu-guard enable\n";
	            $data .= "no shutdown\n";
	            $data .= "exit\n";
	            
	            $mac = preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace([':', '.', '-'], '', $gw->mac));
	            
	            $data .= "mac address-table static {$mac} forward interface port1.0.{$port} vlan 3\n";
	            
// 	            $data .= "interface port1.0.{$port}\n";
// 	            $data .= "no service-policy input 501M\n";
// 	            $data .= "service-policy input 800M\n";
// 	            $data .= "egress-rate-limit 820032\n";
// 	            $data .= "description {$host->getMixName(false)}\n";
	        }
	        $data .= "end\n";
	        $data .= "wr\n";
	        $fileConf = $pathConf . '/xSeries/' . $devX->ips[0]->ip . '.cfg';
	        file_put_contents($fileConf, $data);
	    }
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