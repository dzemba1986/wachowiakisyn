<?php
use yii\widgets\DetailView;
use backend\models\Swith;
/**
 * @var Swith $modelDevice
 */
echo DetailView::widget([
	'model' => $modelDevice,
	'options' => [
			'class' => 'table table-bordered detail-view',
	],
	'attributes' => [
		'id',	
		[
			'label' => 'Adres',
			'value' => $modelDevice->modelAddress->toString()
		],
		[
			'label' => 'Status',
			'value' => $modelDevice->status ? 'Aktywny' : 'Nieaktywny'
		],
		[
			'label' => 'Typ',
			'value' => $modelDevice->modelType->name
		],
		[
			'label' => 'Mac',
			'value' => $modelDevice->mac,
		],
		'serial',
		[
			'label' => 'Rodzaj',
			'value' => $modelDevice->distribution ? 'szkieletowy' : 'dostępowy',
		],
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
