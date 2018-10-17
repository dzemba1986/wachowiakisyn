<div class="col-md-4">

	<table class="table table-bordered detail-view" id="w0">
		<tbody>
			<?php foreach ($modelIps as $modelIp) : ?>
			<tr><th>VLAN <?= $modelIp->modelSubnet->modelVlan->id; ?></th><td><?= $modelIp->ip; ?></td></tr>
			<?php endforeach; ?>
		</tbody>
	</table>

</div>