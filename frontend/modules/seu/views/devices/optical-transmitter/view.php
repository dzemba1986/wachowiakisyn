<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var backend\models\OpticalTransmitter $device
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
	    [
	        'label' => 'Poziom sygnału we.',
	        'value' => $device->input_level . ' dBuV'
	    ],
	    [
	        'label' => 'Poziom mocy wy.',
	        'value' => $device->output_power . ' dBm'
	    ],
	    [
	        'label' => 'Tłumienie',
	        'value' => $device->insertion_loss . ' dB'
	    ],
		[
			'label' => 'Mac',
			'value' => $device->mac,
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

echo Html::beginTag('div', ['class' => 'col-md-5']);
echo Html::beginTag('table', ['class' => 'table table-striped table-bordered detail-view']);
echo Html::beginTag('tbody');
foreach ($device->vlansToIps as $vlanToIp) {
    
    echo '<tr>';
    echo "<th>VLAN {$vlanToIp['vlan_id']}</th>";
    echo "<td>{$vlanToIp['ip']}</td>";
    echo '</tr>';
}
echo Html::endTag('tbody');
echo Html::endTag('table');
echo Html::endTag('div');
?>