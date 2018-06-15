<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var backend\models\Virtual $device
 */

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
	        'label' => 'DHCP',
	        'value' => $device->dhcp ? '<font color="green">Tak</font>' : '<font color="red">Nie</font>',
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
            'value' => Html::button('Dodaj', ['class' => 'copy', 'data-clipboard-text' => $add]) . Html::button('Usuń', ['class' => 'copy', 'data-clipboard-text' => $drop]) . ' ' . Html::tag('div', '', ['id' => 'message']),
            'format' => 'raw',
            'visible' => $device->status && $device->ips
        ]
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
