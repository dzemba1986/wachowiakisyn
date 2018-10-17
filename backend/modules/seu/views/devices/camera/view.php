<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var string $address
 * @var string $type
 * @var string $model
 * @var string $manufacturer
 * @var array $vlansToIps[]
 * @var string $parentIp
 * @var string $portName
 * @var string $add
 * @var string $drop
 * @var backend\models\Camera $device
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
	        'value' => $device->status ? '<font color="green">Aktywny</font>' : '<font color="red">Nieaktywny</font>',
	        'format' => 'raw'
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
			'label' => 'Monitoring',
			'value' => $device->alias,
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
	    [
	        'label' => 'Opcje',
	        'value' => Html::a('Podgląd', 'http://' . $device->vlansToIps[0]['ip'] . '/image', ['target'=>'_blank']) . ' | ' . Html::a('Reboot', 'http://' . $device->vlansToIps[0]['ip'] . '/command/main.cgi?System=reboot', ['target'=>'_blank']),
	        'format' => 'raw',
	        'visible' => $device->status && $device->hasIps
	    ]
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

$js = <<<JS
$(function() {
	var clipboard = new Clipboard('.copy');
	
	clipboard
        .on('success', function(e) {
            $.growl.notice({ message: 'Skrypt w schowku'});
            clipboard.destroy();
        })
        .on('error', function(e) {
            $.growl.error({ message: 'Brak skryptu w schowku'});
            clipboard.destroy();
        });
});
JS;
$this->registerJs($js);
?>