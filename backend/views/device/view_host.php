<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var backend\models\Host $device
 */

require_once '_modal_change_mac.php';

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
		    'value' => Html::a($device->mac, Url::to("http://172.20.4.17:701/index.php?sourceid=3&filter=clientmac%3A%3D" . base_convert(preg_replace('/:/', '', $device->mac), 16, 10) . "&search=Search"), ['target'=>'_blank']) . ' ' . Html::a('Zmień', Url::toRoute(['device/change-mac', 'id' => $device->id]),['class' => 'change-mac']),
		    'format' => 'raw'
		],
		[
		    'label' => 'Skrypty',
		    'value' => Html::button('Dodaj', ['class' => 'copy', 'data-clipboard-text' => $add]) . Html::button('Usuń', ['class' => 'copy', 'data-clipboard-text' => $drop]) . ' ' . Html::tag('div', '', ['id' => 'message']),
		    'format' => 'raw',
		]
	]
]);

echo Html::label('Umowy :');
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->connections as $connection) {
    
    $url = Html::a('Zamknij', Url::to(['connection/close', 'id' => $connection->id]));
	echo '<tr>';
	echo "<th>{$connection->type->name}</th>";
	echo "<td>{$url}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';

echo '<div class="col-md-5">';
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->ips as $ip) {
    
    echo '<tr>';
    echo "<th>VLAN {$ip->subnet->vlan->id}</th>";
    echo "<td>{$ip->ip}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';


$js = <<<JS
$(function(){
	var clipboard = new Clipboard('.copy');
	
	clipboard.on('success', function(e) {
		if(e.trigger.textContent == 'Dodaj')
			$('#message').text('Skopiowano ADD');
		else if(e.trigger.textContent == 'Usuń')
			$('#message').text('Skopiowano DROP');
	}).on('error', function(e) {
        $('#message').text('NIE SKOPIOWANO');
    });


    $('.change-mac').on('click', function(event){
        
		$('#modal-change-mac').modal('show')
			.data('mac', '{$device->mac}')
			.find('#modal-content-change-mac')
			.load($(this).attr('href'));

		//potrzebne by okno modal nie blokowało się
		$("#device_desc").css("position", "absolute");
	
        return false;
	});

	//włącza spowtorem przesówanie opisu urządzenia po zmianie mac
	$('#modal-change-mac').on('hidden.bs.modal', function () {
		$("#device_desc").css("position", "fixed");
	});
});
JS;
$this->registerJs($js);
?>