<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var backend\models\Router $device
 */

echo '<div class="col-md-5">';
echo DetailView::widget([
	'model' => $device,
	'options' => [
			'class' => 'table table-bordered detail-view',
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

echo '<div class="col-md-5">';
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->ips as $ip) {
    
    $url = Html::a($ip->ip, "ssh://{$ip->ip}");
    echo '<tr>';
    echo "<th>VLAN {$ip->subnet->vlan->id}</th>";
    echo "<td>{$url}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';
?>