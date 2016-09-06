<?php
use yii\widgets\DetailView;
?>

<div class="col-md-4">

<?php
$attributes = [];
$index = 0;

foreach ($modelIps as $modelIp){
	$attributes[$index] = [
		'label' => $modelIp->modelSubnet->modelVlan->id,
		'value' => $modelIp->ip	
	];
	
	echo DetailView::widget([
			'model' => $modelIp,
			'options' => [
					'class' => 'col-md-6 table table-striped table-bordered detail-view',
			],
			'attributes' => $attributes
	]);
	
	$index++;
}

?>
</div>