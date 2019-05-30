<?php
use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var backend\models\Virtual $device
 * @var yii\web\View $this
 */

GrowlAsset::register($this);

$attributes = [
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
        'label' => 'Typ',
        'value' => $device->typeName
    ],
];

if ($device->parent->type_id == 2) {
    array_push($attributes, [
        'label' => 'Przełącznik',
        'value' => Html::a($device->parent->type_id == 2 ? $device->parent->firstIp : 'Splitter', "ssh://{$device->parent->firstIp}:22222") . ' - ' . $device->parent->portName,
        'format' => 'raw'
    ]);
}

array_push($attributes, [
    'label' => 'Mac',
    'value' =>  Html::a($device->mac, Url::to(['change-mac', 'id' => $device->id]), ['class' => 'change-mac']),
    'format' => 'raw',
    'visible' => $device->status
]);

if ($device->parent->type_id == 2) {
    array_push($attributes, [
        'label' => 'Skrypty',
        'value' => Html::button('Dodaj', ['class' => 'copy', 'data-clipboard-text' => $device->configAdd()]) . Html::button('Usuń', ['class' => 'copy', 'data-clipboard-text' => $device->configDrop()]) . ' ' . Html::tag('div', '', ['id' => 'message']),
        'format' => 'raw',
        'visible' => $device->status && $device->ips
    ]);
}
echo Html::beginTag('div', ['class' => 'col-md-5']);
echo DetailView::widget([
	'model' => $device,
	'attributes' => $attributes,
]);
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'col-md-5']);
echo Html::beginTag('table', ['class' => 'table table-striped table-bordered detail-view']);
echo Html::beginTag('tbody');
foreach ($device->vlansToIps as $vlanToIp) {
    
    $link = Html::a($vlanToIp['ip'], Url::to("http://172.20.4.17:701/index.php?sourceid=3&filter=clientmac%3A%3D" . base_convert(preg_replace('/:/', '', $device->mac), 16, 10) . "&search=Search"), ['id' => 'check-dhcp','target'=>'_blank']);
    echo '<tr>';
    echo "<th>VLAN {$vlanToIp['vlan_id']}</th>";
    echo "<td>{$link}</td>";
    echo '</tr>';
}
echo Html::endTag('tbody');
echo Html::endTag('table');
echo Html::endTag('div');

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


    $('.change-mac').on('click', function(event) {
        
		$('#modal-sm').modal('show')
			.find('#modal-sm-content')
			.load($(this).attr('href'));
	
        return false;
	});
});
JS;
$this->registerJs($js);
?>
