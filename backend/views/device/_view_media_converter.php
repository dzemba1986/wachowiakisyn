<?php
use yii\widgets\DetailView;

echo DetailView::widget([
	'model' => $modelDevice,
	'options' => [
			'class' => 'table table-bordered detail-view',
	],
	'attributes' => [
		'id',	
		[
			'label' => 'Adres',
			'value' => $modelDevice->modelAddress->fullDeviceAddress
		],
		[
			'label' => 'Status',
			'value' => $modelDevice->status ? 'Aktywny' : 'Nieaktywny'
		],
		[
			'label' => 'Typ',
			'value' => $modelDevice->modelType->name
		],
		'serial',
		[
			'label' => 'Model',
			'value' => $modelDevice->modelModel->name,
		],
		[
			'label' => 'Producent',
			'value' => $modelDevice->modelManufacturer->name,
		],
	]
]);
?>
