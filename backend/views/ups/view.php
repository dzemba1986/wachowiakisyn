<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var backend\models\MediaConverter $device
 */

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
    
    $url = Html::a($ip->ip, "http://{$ip->ip}", ['target'=>'_blank']);
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
