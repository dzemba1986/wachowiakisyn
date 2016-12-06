<?php

namespace backend\models;

use yii\base\Model;
use yii\validators\IpValidator;

class Dhcp extends Model
{
	private $path;
	
	public static function generateFile($subnet = null){
		
		echo \Yii::getAlias('@console/dhcp');
		$test = ob_get_contents();
// var_dump($test); exit();		
		$sysout = system('rm ' . $test . '/subnets/*');
		$updateFile = $test . '/subnets/.update_notify';
// 		var_dump($test); exit();
		$dhcpSubnets = Subnet::find()->where(['id' => $subnet, 'dhcp' => true])->all();
		
		$ipValidator = new IpValidator(['ipv6' => false]);
		
		foreach ($dhcpSubnets as $dhcpSubnet){
			
			//$blockIp = new \IPv4Block($dhcpSubnet->ip);
			
			//var_dump($ipValidator->validateAttribute($dhcpSubnet, 'ip')); exit();
			//if($ipValidator->validateAttribute($dhcpSubnet, 'ip')){
				
				$data = "# PODSIEC " . $dhcpSubnet->desc . "\n
#######################################\n
#         INTERNET - ADRESACJA\n
#######################################\n
\n						
subnet " . $dhcpSubnet->blockIp->getFirstIp() . ' netmask ' . $dhcpSubnet->blockIp->getMask() . " {\n";
				$options = $dhcpSubnet->generateOptionsDhcp();
				foreach ($options as $option){
					$modelDhcpOption = DhcpOption::findOne($option['option']);
					$data .= "\t" . $modelDhcpOption->name . " " . $option['value'].";\n";
				}
				
				$data .= "\tdefault-lease-time " . $options[49]['value'] . ";\n
\tmax-lease-time " . $options[49]['value'] . ";\n
\tmin-lease-time 7200;\n
\n
\n		
\t#######################################\n
\t# USERS\n
\t#######################################\n\n";
				
				$modelsIp = Ip::find()->joinWith('modelDevice')->where(['subnet' => $dhcpSubnet->id, 'type' => 5])->orderBy('ip')->all();
				foreach ($modelsIp as $modelIp){
					$data .= "\thost " . $modelIp->modelDevice->id . " {\n
\t\thardware ethernet " .$modelIp->modelDevice->mac . ";\n
\t\tfixed-address " . $modelIp->ip . ";\n
\t}\n";
					//var_dump($modelIP->modelDevice);
				}
				$data .= "}";
// 				exit();
				$fileConfig = $test . '/subnets/' . iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $dhcpSubnet->desc) . '.conf';
				$file = fopen($fileConfig, 'w');
				fwrite($file, $data);
				fclose($file);
				
// 			} else
// 				return 'Nieprawidłowy adres ip podsieci';
		}
		
		$file = fopen($updateFile, "w");
		fwrite($file, time());
		fclose($file);
		
		ob_end_clean();
	}
}
