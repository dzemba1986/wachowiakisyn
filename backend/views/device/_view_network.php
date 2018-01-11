<?php
use yii\widgets\DetailView;
?>

<div class="col-md-4">

<?php
$attributes = [];
$index = 0;

foreach ($ips as $ip){
	$attributes[$index] = [
		'label' => $ip->modelSubnet->modelVlan->id,
		'value' => $ip->ip	
	];
	
	echo DetailView::widget([
			'model' => $ip,
			'options' => [
					'class' => 'col-md-6 table table-striped table-bordered detail-view',
			],
			'attributes' => $attributes
	]);
	
	$index++;
}

?>
</div>