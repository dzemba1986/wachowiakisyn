<?php

namespace console\controllers;

use yii\console\Controller;
use backend\models\Dhcp;

class DhcpController extends Controller {
	
	// The command "yii example/create test" will call "actionCreate('test')"
	public function actionGenerate() {
		
		Dhcp::generateFile();
	}

	// The command "yii example/index city" will call "actionIndex('city', 'name')"
	// The command "yii example/index city id" will call "actionIndex('city', 'id')"
	//public function actionIndex($category, $order = 'name') { ... }

	// The command "yii example/add test" will call "actionAdd(['test'])"
	// The command "yii example/add test1,test2" will call "actionAdd(['test1', 'test2'])"
	//public function actionAdd(array $name) { ... }
}
?>