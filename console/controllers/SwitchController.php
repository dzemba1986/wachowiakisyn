<?php

namespace console\controllers;

use common\models\seu\devices\Swith;
use yii\console\Controller;


class SwitchController extends Controller {
	
	public function actionList() {
		
		$switches = Swith::find()->select('device.id, ip, model_id, model.name, layer3')->joinWith(['mainIp', 'model'])
            ->where(['<>', 'address_id', 1])->orderBy('ip')->asArray()->all();
		
        $ipsL3 = $ipsL3_172 = $ipsL3_10 = $ipsx900 = $ipsx900_172 = $ipsx900_10 = $ips8000GS = $ips8000GS_24 = $ips8000GS_48 = '';
        $ipsx210 = $ipsx210_10 = $ipsx210_172 = $ipsx230 = $ipsx230_10 = $ipsx230_172 = $ipsx510 = $ipsx510_10 = $ipsx510_172 = $ipsEC = '';
        
		foreach ($switches as $switch) {
		    
		    if ($switch['layer3']) {
		        $ipsL3 .= $switch['ip'] . "\n";
		        if (strpos($switch['ip'], '172.20.') === 0)  $ipsL3_172 .= $switch['ip'] . "\n";
		        elseif (strpos($switch['ip'], '10.') === 0) $ipsL3_10 .= $switch['ip'] . "\n";
		    }
		    
		    if (preg_match('/x900/', $switch['name'])) {
		        $ipsx900 .= $switch['ip'] . "\n";
		        if (strpos($switch['ip'], '172.20.') === 0) $ipsx900_172 .= $switch['ip'] . "\n";
		        elseif (strpos($switch['ip'], '10.') === 0) $ipsx900_10 .= $switch['ip'] . "\n";
		    }
		    
		    if (preg_match('/8000GS/', $switch['name'])) {
		        $ips8000GS .= $switch['ip'] . "\n";
		        if (preg_match('/8000GS\/24/', $switch['name'])) $ips8000GS_24 .= $switch['ip'] . "\n";
		        elseif (preg_match('/8000GS\/48/', $switch['name'])) $ips8000GS_48 .= $switch['ip'] . "\n";
		    }
		    
		    if (preg_match('/x210/', $switch['name'])) {
		        $ipsx210 .= $switch['ip'] . "\n";
		        if (strpos($switch['ip'], '172.20.') === 0) $ipsx210_172 .= $switch['ip'] . "\n";
		        elseif (strpos($switch['ip'], '10.') === 0) $ipsx210_10 .= $switch['ip'] . "\n";
		    }
		    
		    if (preg_match('/x230/', $switch['name'])) {
		        $ipsx230 .= $switch['ip'] . "\n";
		        if (strpos($switch['ip'], '172.20.') === 0) $ipsx230_172 .= $switch['ip'] . "\n";
		        elseif (strpos($switch['ip'], '10.') === 0) $ipsx230_10 .= $switch['ip'] . "\n";
		    }
		    
		    if (preg_match('/x510/', $switch['name'])) {
		        $ipsx510 .= $switch['ip'] . "\n";
		        if (strpos($switch['ip'], '172.20.') === 0) $ipsx510_172 .= $switch['ip'] . "\n";
		        elseif (strpos($switch['ip'], '10.') === 0) $ipsx510_10 .= $switch['ip'] . "\n";
		    }
		    
		    if (preg_match('/EC/', $switch['name'])) $ipsEC .= $switch['ip'] . "\n";
		}
		
		$contentFiles = [];
		$contentFiles[\Yii::getAlias('@console/device/lists/L3')] = $ipsL3;
		$contentFiles[\Yii::getAlias('@console/device/lists/L3-172')] = $ipsL3_172;
		$contentFiles[\Yii::getAlias('@console/device/lists/L3-10')] = $ipsL3_10;
		$contentFiles[\Yii::getAlias('@console/device/lists/x900')] = $ipsx900;
		$contentFiles[\Yii::getAlias('@console/device/lists/x900-172')] = $ipsx900_172;
		$contentFiles[\Yii::getAlias('@console/device/lists/x900-10')] = $ipsx900_10;
		$contentFiles[\Yii::getAlias('@console/device/lists/8000GS')] = $ips8000GS;
		$contentFiles[\Yii::getAlias('@console/device/lists/8000GS-24')] = $ips8000GS_24;
		$contentFiles[\Yii::getAlias('@console/device/lists/8000GS-48')] = $ips8000GS_48;
		$contentFiles[\Yii::getAlias('@console/device/lists/x210')] = $ipsx210;
		$contentFiles[\Yii::getAlias('@console/device/lists/x210-172')] = $ipsx210_172;
		$contentFiles[\Yii::getAlias('@console/device/lists/x210-10')] = $ipsx210_10;
		$contentFiles[\Yii::getAlias('@console/device/lists/x230')] = $ipsx230;
		$contentFiles[\Yii::getAlias('@console/device/lists/x230-172')] = $ipsx230_172;
		$contentFiles[\Yii::getAlias('@console/device/lists/x230-10')] = $ipsx230_10;
		$contentFiles[\Yii::getAlias('@console/device/lists/x510')] = $ipsx510;
		$contentFiles[\Yii::getAlias('@console/device/lists/x510-172')] = $ipsx510_172;
		$contentFiles[\Yii::getAlias('@console/device/lists/x510-10')] = $ipsx510_10;
		$contentFiles[\Yii::getAlias('@console/device/lists/ecs')] = $ipsEC;

        foreach ($contentFiles as $file => $content) {
            file_put_contents($file, $content, LOCK_EX);
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
    	            ['10.111.233.4', 1, 'default.cfg', 4, 'xSeries_' . $x['ip'] . '.rtf', 1]
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
}
?>