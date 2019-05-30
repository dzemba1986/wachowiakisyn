<?php
use kartik\growl\GrowlAsset;
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

GrowlAsset::register($this);

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
	        'label' => 'Ip',
	        'value' => Html::a("{$device->vlansToIps[0]['ip']}", "http://{$device->vlansToIps[0]['ip']}", ['target'=>'_blank']) . " [ vlan{$device->vlansToIps[0]['vlan_id']} ]",
	        'format' => 'raw',
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
	        'value' => Html::a($device->parent->firstIp, "ssh://{$device->parent->firstIp}:22222") . " [ {$device->parent->portName} ]",
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
	    ],
	    'desc',
	]
]);
echo Html::endTag('div');

$js = <<<JS
$(function() {
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