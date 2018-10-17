<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var backend\models\Host $device
 */

echo $this->renderFile('@backend/views/modal/modal_sm.php');

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
			'label' => 'Typ',
			'value' => $device->typeName
		],
	]
]);
echo Html::label('Umowy :', null, ['hidden' => !$device->status]);
echo '<table class="table table-striped table-bordered detail-view">';
echo '<tbody>';
foreach ($device->connectionsTypeNameToSoaId as $connection) {
    
    $link = Html::a('Zamknij', Url::to(['connection/close', 'id' => $connection['id']]), ['class' => 'close-connection']);
	echo '<tr>';
	echo "<th>{$connection['name']} ({$connection['soa_id']})</th>";
	echo "<td>{$link}</td>";
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';
echo '</div>';

$js = <<<JS
$(function() {
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