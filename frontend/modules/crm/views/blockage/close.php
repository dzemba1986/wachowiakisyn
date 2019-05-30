<?php

use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\base\View $this
 * @var backend\models\Host $host
 */
GrowlAsset::register($this);

ActiveForm::begin([
    'id' => 'close-bockage'
]);
    echo Html::tag('p', 'Czy zamknąć blokadę/rezerwację?');
    
    echo Html::submitButton('Tak', ['class' => 'btn btn-success']);
  
    echo Html::button('Nie', ['class' => 'no btn btn-danger']);

ActiveForm::end();

$js = <<<JS
$(function() {

    $('#modal-title').html('Zamknij blokadę/rezerwację');
        
    $('.no').click(function() {
		$('#modal-sm').modal('hide');
    });

    $('#close-bockage').on('beforeSubmit', function(e) {
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result) {
	 		if(result[0] == 1) {
				$('#modal').modal('hide');
                $('#calendar').fullCalendar('refetchEvents');
	 		}
	 		else {
                $('#modal').modal('hide');
                $.notify(result[1], {
                    type : 'danger',
                    placement : { from : 'top', align : 'right'},
                });
	 		}
	 	}).fail(function() {
	 		console.log('server error');
	 	});
		return false;				
	});	
});
JS;

$this->registerJs($js);
?>