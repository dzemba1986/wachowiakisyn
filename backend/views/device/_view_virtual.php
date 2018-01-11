<?php
use yii\widgets\DetailView;
/**
 * @var Virtual $modelDevice
 */
echo DetailView::widget([
	'model' => $device,
	'options' => [
			'class' => 'table table-bordered detail-view',
	],
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => ''
    ],
	'attributes' => [
		'id',	
		[
			'label' => 'Adres',
			'value' => $device->address->toString()
		],
		[
			'label' => 'Status',
			'value' => $device->status ? 'Aktywny' : 'Nieaktywny'
		],
		[
			'label' => 'Typ',
			'value' => $device->type->name
		],
		[
			'label' => 'Mac',
			'value' => $device->mac,
		]
	]
]);
?>
