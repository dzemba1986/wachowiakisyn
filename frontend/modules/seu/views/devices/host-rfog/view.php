<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var backend\models\Host $device
 */

echo $this->renderFile('@app/views/modal/modal_sm.php');

echo Html::beginTag('div', ['class' => 'col-md-5']);
echo DetailView::widget([
	'model' => $device,
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => ''
    ],
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
	    [
	        'label' => 'Moc IN',
	        'value' => $device->input_power ? $device->input_power . ' dBm' : null,
        ],
	    'desc',
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