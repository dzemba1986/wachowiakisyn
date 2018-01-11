<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/**
 * @var Host $modelDevice
 */
?>

<!-------------------------------------------- otwórz kalendarz okno modal -------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal-change-mac',	
		'header' => '<center><h4>Zmiana MAC</h4></center>',
		'size' => 'modal-sm',
		'options' => [
			'tabindex' => false, // important for Select2 to work properly
			'enforceFocus' => false,
			'style' => ['position' => 'fixed'],
			'backdrop' => false,
			//'class' => 'modal bottom-sheet'
		],	
	]);
	
	echo "<div id='modal-content-change-mac'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->  

<?php 
echo DetailView::widget([
	'model' => $device,
	'options' => [
			'class' => 'table table-bordered detail-view',
	],
	'attributes' => [
		'id',	
		[
			'label' => 'Adres',
			'value' => $device->address->toString()
		],
		[
			'label' => 'Konfiguracja',
			'value' => $device->start_date
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
			'label' => 'Mac',
			'format' => 'raw',	
			'value' => Html::a($device->mac, Url::to("http://172.20.4.17:701/index.php?sourceid=3&filter=clientmac%3A%3D" . base_convert(preg_replace('/:/', '', $device->mac), 16, 10) . "&search=Search"), ['target'=>'_blank']) . ' ' . Html::a('Zmień', Url::toRoute(['device/change-mac', 'id' => $device->id]),['class' => 'change-mac'])
		],
	]
]);
?>

<script>

$(function(){
	$('.change-mac').on('click', function(event){
        
		$('#modal-change-mac').modal('show')
			.data('mac', '<?= $device->mac ?>')
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

</script>