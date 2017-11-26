<?php
use backend\models\Device;
?>
<div class="col-md-10">


	<?php
	// var_dump(get_class($modelDevice)); exit();
	switch (get_class($modelDevice)){
		case 'backend\models\Host':
			echo $this->render('_script_host', [
				'modelDevice' => $modelDevice,
				'modelIps' => $modelIps
			]);
			break;
// 		case 'backend\models\Router':
// 			echo $this->render('_script_router', [
// 				'modelDevice' => $modelDevice,
// 			]);
// 			break;
		case 'backend\models\Swith':
	
			$arHosts = Device::find()->joinWith('modelTree')->where(['parent_device' => $modelDevice->id])->andWhere(['type' => [5]])->orderBy('parent_port')->all();
			
			echo $this->render('_script_switch', [
				'arHosts' => $arHosts,
				'modelDevice' => $modelDevice,
			]);
			break;
		case 'backend\models\GatewayVoip':
			 
			echo $this->render('_script_gateway_voip', [
				'modelDevice' => $modelDevice,
			]);
			break;
// 		case 'backend\models\Camera':
		
// 			echo $this->render('_script_camera', [
// 				'modelDevice' => $modelDevice,
// 			]);
// 			break;
// 		case 'backend\models\Server':
		
// 			echo $this->render('_script_server', [
// 				'modelDevice' => $modelDevice,
// 			]);
// 			break;
// 		case 'backend\models\Virtual':
			 
// 			echo $this->render('_script_virtual', [
// 				'modelDevice' => $modelDevice,
// 			]);
// 			break;
// 		case 'backend\models\MediaConverter':
				
// 			echo $this->render('_script_media_converter', [
// 				'modelDevice' => $modelDevice,
// 			]);
// 			break;
	}
	?>
</div>