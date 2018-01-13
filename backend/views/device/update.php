<?php
switch (get_class($device)){
	case 'backend\models\Host':
		echo $this->render('_update_host', [
            'device' => $device,
            'address' => $address,
		]);
		break;
	case 'backend\models\Router':
		echo $this->render('_update_router', [
			'device' => $device,
			'address' => $address,
		]);
		break;
	case 'backend\models\Swith':

		echo $this->render('_update_switch', [
			'device' => $device,
			'address' => $address,
		]);
		break;
	case 'backend\models\GatewayVoip':
		 
		echo $this->render('_update_gateway_voip', [
			'device' => $device,
			'address' => $address,
		]);
		break;
	case 'backend\models\Camera':
	
		echo $this->render('_update_camera', [
			'device' => $device,
			'address' => $address,
		]);
		break;
	case 'backend\models\Server':
	
		echo $this->render('_update_server', [
			'device' => $device,
			'address' => $address,
		]);
		break;
	case 'backend\models\Virtual':
		 
		echo $this->render('_update_virtual', [
			'device' => $device,
			'address' => $address,
		]);
		break;
	case 'backend\models\MediaConverter':
			
		echo $this->render('_update_media_converter', [
			'device' => $device,
			'address' => $address,
		]);
		break;
}
?>