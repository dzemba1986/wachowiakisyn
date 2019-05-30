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
	        'label' => 'SMTP',
	        'value' => $device->smtp ? '<font color="green">Tak</font>' : '<font color="red">Nie</font>',
	        'format' => 'raw'
	    ],
		[
			'label' => 'Typ',
			'value' => $device->typeName
		],
	    [
	        'label' => 'Ip',
	        'value' => Html::a("{$device->vlansToIps[0]['ip']}", Url::to("http://172.20.4.17:701/index.php?sourceid=3&filter=clientmac%3A%3D" . base_convert(preg_replace('/:/', '', $device->mac), 16, 10) . "&search=Search"), ['id' => 'check-dhcp','target'=>'_blank']) . " [ vlan{$device->vlansToIps[0]['vlan_id']} ]",
	        'format' => 'raw',
        ],
        [
            'label' => 'Mac',
            'value' =>  Html::a($device->mac, Url::to(['change-mac', 'id' => $device->id]), ['class' => 'change-mac']),
            'format' => 'raw',
            'visible' => $device->status
        ],
	    [
	        'label' => 'Przełącznik',
	        'value' => Html::a($device->configParent->firstIp, "ssh://{$device->configParent->firstIp}:22222") . " [ {$device->configParent->portName} ]",
	        'format' => 'raw'
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
                    if ({$device->configParent->configType} == 1) {
                        $( '#modal-sm' ).modal('show').find( '#modal-sm-content' ).load($(this).attr('link'));
                    }

                    return false;
                "
		    ]),
		    'format' => 'raw',
		    'visible' => $device->status && $device->hasIps
		],
		'desc'
	]
]);

echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'col-md-5']);
echo Html::beginTag('table', ['class' => 'table table-striped table-bordered detail-view']);
echo Html::beginTag('tbody');
foreach ($device->connectionsTypeNameToSoaId as $connection) {
    
    $link = Html::a('Zamknij', Url::to(['/soa/connection/close', 'id' => $connection['id']]), ['class' => 'close-connection']);
    echo Html::beginTag('tr');
    echo Html::tag('th', "{$connection['name']} ({$connection['soa_id']})");
    echo Html::tag('td', $link);
    echo Html::endTag('tr');
}
echo Html::endTag('tbody');
echo Html::endTag('table');
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