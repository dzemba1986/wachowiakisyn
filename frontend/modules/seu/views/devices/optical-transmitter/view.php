<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var backend\models\OpticalTransmitter $device
 */

echo '<div class="col-md-5">';
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
	]
]);
echo '</div>';

echo '<div class="col-md-5">';
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->vlansToIps as $vlanToIp) {
    
    $url = Html::a($vlanToIp['ip'], "http://{$vlanToIp['ip']}", ['target'=>'_blank']);
    echo '<tr>';
    echo "<th>VLAN {$vlanToIp['vlan']}</th>";
    echo "<td>{$url}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';
?>