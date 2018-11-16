<?php

use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var backend\models\Host $device
 */

GrowlAsset::register($this);

echo $this->renderFile('@app/views/modal/modal_sm.php');

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
	        'label' => 'SMTP',
	        'value' => $device->smtp ? '<font color="green">Tak</font>' : '<font color="red">Nie</font>',
	        'format' => 'raw'
	    ],
		[
			'label' => 'Typ',
			'value' => $device->typeName
		],
	    [
	        'label' => 'Przełącznik',
	        'value' => Html::a($device->parent->firstIp, "ssh://{$device->parent->firstIp}:22222") . ' - ' . $device->parent->portName,
	        'format' => 'raw'
	    ],
		[
			'label' => 'Mac',
		    'value' =>  Html::a($device->mac, Url::to(['change-mac', 'id' => $device->id]), ['class' => 'change-mac']),
		    'format' => 'raw',
            'visible' => $device->status
		],
		[
		    'label' => 'Skrypty',
		    'value' => Html::button('Dodaj', [
		        'class' => 'copy',
		        'data-clipboard-text' => $device->configAdd()
		    ]) . Html::button('Usuń', [
		        'class' => 'copy',
		        'link' => Url::to(['send-config', 'id' => $device->id, 'type' => 'drop']),
		        'data-clipboard-text' => $device->configDrop(),
		        'onclick' => "
                    if ({$device->parent->configType} == 1) {
                        $( '#modal-sm' ).modal('show').find( '#modal-sm-content' ).load($(this).attr('link'));
                    }

                    return false;
                "
		    ]),
		    'format' => 'raw',
		    'visible' => $device->status && $device->hasIps
		]
	]
]);
echo Html::label('Umowy :', null, ['hidden' => !$device->status]);
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->connectionsTypeNameToSoaId as $connection) {
    
    $link = Html::a('Zamknij', Url::to(['/soa/connection/close', 'id' => $connection['id']]), ['class' => 'close-connection']);
	echo '<tr>';
	echo "<th>{$connection['name']} ({$connection['soa_id']})</th>";
	echo "<td>{$link}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div class="col-md-5">';
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->vlansToIps as $vlanToIp) {
    
    $link = Html::a($vlanToIp['ip'], Url::to("http://172.20.4.17:701/index.php?sourceid=3&filter=clientmac%3A%3D" . base_convert(preg_replace('/:/', '', $device->mac), 16, 10) . "&search=Search"), ['id' => 'check-dhcp','target'=>'_blank']);
    echo '<tr>';
    echo "<th>VLAN {$vlanToIp['vlan_id']}</th>";
    echo "<td>{$link}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';


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

    $('.change-mac').on('click', function(event) {
        
		$('#modal-sm').modal('show')
			.find('#modal-sm-content')
			.load($(this).attr('href'));
	
        return false;
	});

    $('.close-connection').on('click', function(event) {
        
		$('#modal-sm').modal('show')
			.find('#modal-sm-content')
			.load($(this).attr('href'));

        return false;
	});
});
JS;
$this->registerJs($js);
?>