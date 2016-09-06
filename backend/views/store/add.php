<?php
// var_dump(get_class($modelDevice)); exit();
switch (get_class($modelDevice)){
	case 'backend\models\Router':
		echo $this->render('_add_router', [
				'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\Swith':

		echo $this->render('_add_switch', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\GatewayVoip':
		 
		echo $this->render('_add_gateway_voip', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\Camera':
	
		echo $this->render('_add_camera', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\Server':
	
		echo $this->render('_add_server', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\Virtual':
		 
		echo $this->render('_add_virtual', [
			'modelDevice' => $modelDevice,
		]);
		break;
	case 'backend\models\MediaConverter':
			
		echo $this->render('_add_media_converter', [
			'modelDevice' => $modelDevice,
		]);
		break;
}
?>
