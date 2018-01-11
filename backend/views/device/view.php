<div class="col-md-10">

	<div class="col-md-6">
	<?php
	// var_dump(get_class($modelDevice)); exit();
	switch (get_class($device)){
		case 'backend\models\Host':
			echo $this->render('_view_host', [
				'device' => $device,
			]);
			break;
		case 'backend\models\Router':
			echo $this->render('_view_router', [
				'device' => $device,
			]);
			break;
		case 'backend\models\Swith':
	
			echo $this->render('_view_switch', [
				'device' => $device,
			]);
			break;
		case 'backend\models\GatewayVoip':
			 
			echo $this->render('_view_gateway_voip', [
				'device' => $device,
			]);
			break;
		case 'backend\models\Camera':
		
			echo $this->render('_view_camera', [
				'device' => $device,
			]);
			break;
		case 'backend\models\Server':
		
			echo $this->render('_view_server', [
				'device' => $device,
			]);
			break;
		case 'backend\models\Virtual':
			 
			echo $this->render('_view_virtual', [
				'device' => $device,
			]);
			break;
		case 'backend\models\MediaConverter':
				
			echo $this->render('_view_media_converter', [
				'device' => $device,
			]);
			break;
	}
	?>
	</div>

	<div class="col-md-5">
		<table class="table table-bordered detail-view" id="w0">
			<tbody>
				<?php foreach ($ips as $ip) : ?>
				<tr>
					<th>VLAN <?= $ip->modelSubnet->modelVlan->id; ?></th>
					<td><?= $ip->ip; ?></td>
					<?php if(get_class($device) == 'backend\models\Swith') :?>
						<td><a href="ssh://<?= $ip->ip; ?>:22222">Zaloguj</a></td>
					<?php elseif (get_class($device) == 'backend\models\GatewayVoip') :?>
						<td><a href="http://<?= $ip->ip; ?>:6666">Zaloguj</a></td>
					<?php endif;?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
</div>