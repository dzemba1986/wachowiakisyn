<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
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
			'value' => $device->typeName
		],
		[
			'label' => 'Mac',
			'value' => $device->mac,
		],
		'serial',
		[
			'label' => 'Rodzaj',
			'value' => $device->distribution ? 'szkieletowy' : 'dostępowy',
		],
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
    
    $url = Html::a($vlanToIp['ip'], "ssh://{$vlanToIp['ip']}:22222");
    echo '<tr>';
    echo "<th>VLAN {$vlanToIp['vlan_id']}</th>";
    echo "<td>{$url}</td>";
    echo '</tr>';
}
echo Html::endTag('tbody');
echo Html::endTag('table');
echo Html::endTag('div');
?>