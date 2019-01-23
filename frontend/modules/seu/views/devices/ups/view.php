<?php
use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var backend\models\Ups $device
 */

GrowlAsset::register($this);

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
	        'label' => 'DHCP',
	        'value' => $device->dhcp ? '<font color="green">Tak</font>' : '<font color="red">Nie</font>',
	        'format' => 'raw'
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
			'label' => 'Model',
			'value' => $device->modelName,
		],
		[
			'label' => 'Producent',
			'value' => $device->manufacturerName,
		],
	    [
	        'label' => 'Przełącznik',
	        'value' => Html::a($device->parent->firstIp, "ssh://{$device->parent->firstIp}:22222") . ' - ' . $device->parent->portName,
	        'format' => 'raw'
        ],
        [
            'label' => 'Skrypty',
            'value' => Html::button('Dodaj', ['class' => 'copy', 'data-clipboard-text' => $device->configAdd()]) . Html::button('Usuń', ['class' => 'copy', 'data-clipboard-text' => $device->configDrop()]),
            'format' => 'raw',
            'visible' => $device->status && $device->hasIps
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
    echo "<th>VLAN {$vlanToIp['vlan_id']}</th>";
    echo "<td>{$url}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';

$js = <<<JS
$(function(){
	var clipboard = new ClipboardJS('.copy');
	
	clipboard
        .on('success', function(e) {
            $.notify('Skrypt w schowku.', {
                type : 'success',
                placement : { from : 'top', align : 'right'},
            });
            clipboard.destroy();
        })
        .on('error', function(e) {
            $.notify('Niepowodzenie skopiowania skryptu.', {
                type : 'danger',
                placement : { from : 'top', align : 'right'},
            });
            clipboard.destroy();
        });
});
JS;
$this->registerJs($js);
?>
