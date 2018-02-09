<?php
use yii\widgets\DetailView;

/**
 * @var backend\models\MediaConverter $device
 */

echo '<div class="col-md-5">';
echo DetailView::widget([
	'model' => $device,
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
		'serial',
		[
			'label' => 'Model',
			'value' => $device->model->name,
		],
		[
			'label' => 'Producent',
			'value' => $device->manufacturer->name,
		],
	]
]);
echo '</div>';
?>
