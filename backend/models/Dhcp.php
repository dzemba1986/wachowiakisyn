<?php

namespace backend\models;

use yii\base\Model;

class Dhcp extends Model
{
	public static function generateFile(Subnet $subnet) {
	    
	    $pathDhcp = \Yii::getAlias('@console/dhcp');
	    $updateFile = $pathDhcp . '/subnets/.update_notify';
	    
	    if ($subnet->dhcp) {
	        $ips = Ip::find()->joinWith('device')->select(['device_id', 'ip', 'mac'])->where([
	            'subnet_id' => $subnet->id, 'dhcp' => true, 'main' => true
	        ])->orderBy('ip')->asArray()->all();
	        
	        $data = "# PODSIEĆ {$subnet->desc}\n\n";
	        $data .= "subnet {$subnet->blockIp->getFirstIp()} netmask {$subnet->blockIp->getMask()} {\n";
	        $data .= "{$subnet->generateOptionsDhcp()}";
	        
	        foreach ($ips as $ip) {
	            $data .= "\thost {$ip['device_id']} {\n";
	            $data .= "\t\thardware ethernet {$ip['device']['mac']};\n";
	            $data .= "\t\tfixed-address {$ip['ip']};\n";
	            $data .= "\t}\n";
	        }
	        
	        $data .= "}";
	        
	        $fileConf = $pathDhcp . '/subnets/' . $subnet->id . '.conf';
	        file_put_contents($fileConf, $data);
	    }
	    
	    file_put_contents($updateFile, time());
	}
}
