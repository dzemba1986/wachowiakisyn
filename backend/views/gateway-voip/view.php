<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var backend\models\GatewayVoip $device
 */
$add = $device->configurationAdd();
$drop = $device->configurationDrop();

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
	        'label' => 'Monitorować',
	        'value' => $device->monitoring ? '<font color="green">Tak</font>' : '<font color="red">Nie</font>',
	        'format' => 'raw'
	    ],
	    [
	        'label' => 'Geolokacja',
	        'value' => $device->geolocation,
	    ],
		[
			'label' => 'Typ',
			'value' => $device->type->name
		],
		[
			'label' => 'Mac',
			'value' => $device->mac,
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
	    [
	        'label' => 'Przełącznik',
	        'value' => Html::a($device->parentIp, "ssh://{$device->parentIp}:22222") . ' - ' . $device->parentPortName,
	        'format' => 'raw'
        ],
        [
            'label' => 'Skrypty',
            'value' => Html::button('Dodaj', ['class' => 'copy', 'data-clipboard-text' => $add]) . Html::button('Usuń', ['class' => 'copy', 'data-clipboard-text' => $drop]) . ' ' . Html::tag('p', '', ['id' => 'message']),
            'format' => 'raw',
        ]
	]
]);
echo '</div>';

echo '<div class="col-md-5">';
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->ips as $ip) {
    
    $url = Html::a($ip->ip, "http://{$ip->ip}:6666");
    echo '<tr>';
    echo "<th>VLAN {$ip->subnet->vlan->id}</th>";
    echo "<td>{$url}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';
?>