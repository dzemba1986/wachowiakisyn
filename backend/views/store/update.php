<?php
// var_dump(get_class($modelDevice)); exit();
switch (get_class($modelDevice)){
	case 'backend\models\Router':
		echo $this->render('_update_router', [
				'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\Swith':

		echo $this->render('_update_switch', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\GatewayVoip':
		 
		echo $this->render('_update_gateway_voip', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\Camera':
	
		echo $this->render('_update_camera', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\Server':
	
		echo $this->render('_update_server', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\Virtual':
		 
		echo $this->render('_update_virtual', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\MediaConverter':
			
		echo $this->render('_update_media_converter', [
			'modelDevice' => $modelDevice,
		]);
		break;
}
?>
