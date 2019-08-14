<?php

namespace console\controllers;

use common\models\seu\network\Dhcp;
use yii\console\Controller;
use common\models\seu\network\Subnet;

class DhcpController extends Controller {
	
	// The command "yii example/create test" will call "actionCreate('test')"
	public function actionGenerate() {
		
	    $subnets = Subnet::findAll(['vlan_id' => [2,4]]);
	    
	    foreach ($subnets as $subnet) {
	        
    		Dhcp::generateFile($subnet);
    		sleep(5);
	    }
	}

	// The command "yii example/index city" will call "actionIndex('city', 'name')"
	// The command "yii example/index city id" will call "actionIndex('city', 'id')"
	//public function actionIndex($category, $order = 'name') { ... }

	// The command "yii example/add test" will call "actionAdd(['test'])"
	// The command "yii example/add test1,test2" will call "actionAdd(['test1', 'test2'])"
	//public function actionAdd(array $name) { ... }
}
?>