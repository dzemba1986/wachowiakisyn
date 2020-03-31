<?php

namespace console\controllers;

use common\models\seu\network\Dhcp;
use yii\console\Controller;

class DhcpController extends Controller {
	
	// The command "yii example/create test" will call "actionCreate('test')"
	public function actionGenerateAllFile() {
		
		Dhcp::generateFileAll();
	}

	// The command "yii example/index city" will call "actionIndex('city', 'name')"
	// The command "yii example/index city id" will call "actionIndex('city', 'id')"
	//public function actionIndex($category, $order = 'name') { ... }

	// The command "yii example/add test" will call "actionAdd(['test'])"
	// The command "yii example/add test1,test2" will call "actionAdd(['test1', 'test2'])"
	//public function actionAdd(array $name) { ... }
}
?>