<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var backend\models\Host $host
 * @var backend\models\Connection $connection
 */

echo $this->renderFile('@backend/views/modal/modal_sm.php');

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
			'value' => $device->type->name
		],
	    [
	        'label' => 'Przełącznik',
	        'value' => Html::a($device->parentIp, "ssh://{$device->parentIp}:22222") . ' - ' . $device->parentPortName,
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
		    'value' => Html::button('Dodaj', ['class' => 'copy', 'data-clipboard-text' => $add]) . Html::button('Usuń', ['class' => 'copy', 'data-clipboard-text' => $drop]),
		    'format' => 'raw',
		    'visible' => $device->status
		]
	]
]);
echo Html::label('Umowy :', null, ['hidden' => !$device->status]);
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->connections as $connection) {
    
    $link = Html::a('Zamknij', Url::to(['connection/close', 'id' => $connection->id]), ['class' => 'close-connection']);
	echo '<tr>';
	echo "<th>{$connection->type->name} ({$connection->soa_id})</th>";
	echo "<td>{$link}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div class="col-md-5">';
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->ips as $ip) {
    
    $link = Html::a($ip->ip, Url::to("http://172.20.4.17:701/index.php?sourceid=3&filter=clientmac%3A%3D" . base_convert(preg_replace('/:/', '', $device->mac), 16, 10) . "&search=Search"), ['id' => 'check-dhcp','target'=>'_blank']);
    echo '<tr>';
    echo "<th>VLAN {$ip->subnet->vlan->id}</th>";
    echo "<td>{$link}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';


$js = <<<JS
$(function(){
	var clipboard = new Clipboard('.copy');
	
	clipboard
        .on('success', function(e) {
            $.growl.notice({ message: 'Skrypt w schowku'});
        })
        .on('error', function(e) {
            $.growl.error({ message: 'Brak skryptu w schowku'});
        });


    $('.change-mac').on('click', function(event) {
        
		$('#modal-sm').modal('show')
			.find('#modal-sm-content')
			.load($(this).attr('href'));
	
        return false;
	});

    $('.close-connection').on('click', function(event) {
        
		$('#modal-change-mac').modal('show')
			.find('#modal-content-change-mac')
			.load($(this).attr('href'));

        return false;
	});
});
JS;
$this->registerJs($js);
?>