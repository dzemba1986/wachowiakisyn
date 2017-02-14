<?php

namespace backend\models;

use yii\base\Model;
use yii\validators\IpValidator;

class Dhcp extends Model
{
	private $path;
	
	public static function generateFile(array $subnets = []){
		
		echo \Yii::getAlias('@console/dhcp');
		$test = ob_get_contents();
		
		$updateFile = $test . '/subnets/.update_notify';
// 		$ipValidator = new IpValidator(['ipv6' => false]);
		
		if (empty($subnets)){
			$dhcpSubnets = Subnet::find()->where(['dhcp' => true])->all();
			$sysout = system('rm ' . $test . '/subnets/*');
		} else {
			$dhcpSubnets = Subnet::find()->where(['and', ['in', 'id', $subnets], ['dhcp' => true]])->all();
		}
		
		foreach ($dhcpSubnets as $dhcpSubnet){
			
			//$blockIp = new \IPv4Block($dhcpSubnet->ip);
			
			//var_dump($ipValidator->validateAttribute($dhcpSubnet, 'ip')); exit();
			//if($ipValidator->validateAttribute($dhcpSubnet, 'ip')){
				
				$data = "# PODSIEC " . $dhcpSubnet->desc . "
#######################################
#         INTERNET - ADRESACJA
#######################################
\n						
subnet " . $dhcpSubnet->blockIp->getFirstIp() . ' netmask ' . $dhcpSubnet->blockIp->getMask() . " {\n";
				$options = $dhcpSubnet->generateOptionsDhcp();
				foreach ($options as $option){
					$modelDhcpOption = DhcpOption::findOne($option['option']);
					$data .= "\t" . $modelDhcpOption->name . " " . $option['value'].";\n";
				}
				
				$data .= "\tdefault-lease-time " . $options[49]['value'] . ";
\tmax-lease-time " . $options[49]['value'] . ";
\tmin-lease-time 7200;
	
\t#######################################
\t# USERS
\t#######################################\n\n";
				
				$modelsIp = Ip::find()->joinWith('modelDevice')->where(['subnet' => $dhcpSubnet->id, 'type' => 5])->orderBy('ip')->all();
				foreach ($modelsIp as $modelIp){
					$data .= "\thost " . $modelIp->modelDevice->id . " {
\t\thardware ethernet " .$modelIp->modelDevice->mac . ";
\t\tfixed-address " . $modelIp->ip . ";
\t}\n";
					//var_dump($modelIP->modelDevice);
				}
				$data .= "}";
// 				exit();
				$sysout = system('rm ' . $test . '/subnets/'  . $dhcpSubnet->id . '.conf');
				
				$fileConfig = $test . '/subnets/' . $dhcpSubnet->id . '.conf';
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
