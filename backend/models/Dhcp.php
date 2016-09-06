<?php

namespace backend\models;

use yii\base\Model;
use yii\validators\IpValidator;

class Dhcp extends Model
{
	private $path;
	
	public function generateFile(){
		
		echo \Yii::getAlias('@console/dhcp');
		$test = ob_get_contents();
		
		//$sysout = system('rm ' . $this->path . '/subnets/*');
		$updateFile = $test . '/subnets/.update_notify';
// 		var_dump($test); exit();
		$dhcpSubnets = Subnet::find()->where(['dhcp' => true])->all();
		
		$ipValidator = new IpValidator(['ipv6' => false]);
		
		foreach ($dhcpSubnets as $dhcpSubnet){
			
			//$blockIp = new \IPv4Block($dhcpSubnet->ip);
			
			//var_dump($ipValidator->validateAttribute($dhcpSubnet, 'ip')); exit();
			//if($ipValidator->validateAttribute($dhcpSubnet, 'ip')){
				
				$data = '# PODSIEC ' . $dhcpSubnet->desc . '
#######################################
#         INTERNET - ADRESACJA
#######################################
						
subnet ' . $dhcpSubnet->blockIp->getFirstIp() . ' netmask ' . $dhcpSubnet->blockIp->getMask() . " {\n";
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
				$fileConfig = $test . '/subnets/' . $dhcpSubnet->desc . '.conf';
				$file = fopen($fileConfig, 'w');
				fwrite($file, $data);
				fclose($file);
				
// 			} else
// 				return 'Nieprawidłowy adres ip podsieci';
		}
	}
}
