<div class="col-md-10">

	<div class="col-md-5">
	<?php
	// var_dump(get_class($modelDevice)); exit();
	switch (get_class($modelDevice)){
		case 'backend\models\Host':
			echo $this->render('_view_host', [
				'modelDevice' => $modelDevice,
			]);
			break;
		case 'backend\models\Router':
			echo $this->render('_view_router', [
				'modelDevice' => $modelDevice,
			]);
			break;
		case 'backend\models\Swith':
	
			echo $this->render('_view_switch', [
				'modelDevice' => $modelDevice,
			]);
			break;
		case 'backend\models\GatewayVoip':
			 
			echo $this->render('_view_gateway_voip', [
				'modelDevice' => $modelDevice,
			]);
			break;
		case 'backend\models\Camera':
		
			echo $this->render('_view_camera', [
				'modelDevice' => $modelDevice,
			]);
			break;
		case 'backend\models\Server':
		
			echo $this->render('_view_server', [
				'modelDevice' => $modelDevice,
			]);
			break;
		case 'backend\models\Virtual':
			 
			echo $this->render('_view_virtual', [
				'modelDevice' => $modelDevice,
			]);
			break;
		case 'backend\models\MediaConverter':
				
			echo $this->render('_view_media_converter', [
				'modelDevice' => $modelDevice,
			]);
			break;
	}
	?>
	</div>

	<div class="col-md-5">
		<table class="table table-bordered detail-view" id="w0">
			<tbody>
				<?php foreach ($modelIps as $modelIp) : ?>
				<tr><th>VLAN <?= $modelIp->modelSubnet->modelVlan->id; ?></th><td><?= $modelIp->ip; ?></td></tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
</div>