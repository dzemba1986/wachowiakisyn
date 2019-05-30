<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var backend\models\Swith $device
 */

echo Html::beginTag('div', ['class' => 'col-md-5']);
echo DetailView::widget([
	'model' => $device,
	'attributes' => [
		'id',	
		[
			'label' => 'Adres',
			'value' => $device->addressName
		],
		[
			'label' => 'Status',
			'value' => $device->status ? 'Aktywny' : 'Nieaktywny'
		],
		[
			'label' => 'Typ',
			'value' => $device->typeName
		],
		'serial',
		[
			'label' => 'Model',
			'value' => $device->modelName,
		],
		[
			'label' => 'Producent',
			'value' => $device->manufacturerName,
		],
        'desc',
	]
]);
echo Html::endTag('div');
?>