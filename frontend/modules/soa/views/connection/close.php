<?php

/**
 * @var yii\web\View $this
 * @var integer $hostId
 * @var yii\widgets\ActiveForm $form
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
	'id' => 'close'
]) ?>
    <p>Jeżeli jest to ostatnia umowa na tym hoście operacja dezaktywuje hosta i zwolni zajęty przez niego adres IP. Czy na pewno?</p>
    
    <?= Html::submitButton('Tak', ['class' => 'btn btn-success']) ?>
  
    <?= Html::button('Nie', ['class' => 'no btn btn-danger']) ?>

<?php ActiveForm::end() ?>

<?php
$urlView = Url::to(['/seu/host-ethernet/tabs-view']);

$js = <<<JS
$(function(){

    $('.modal-header h4').html('Zamknij umowę');
        
    $('.no').click(function() {
		$('#modal-device-sm').modal('hide');
    });

    $('#close').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
				$('#modal-sm').modal('hide');
    			$('#device_desc').load('{$urlView}&id=' + {$hostId});
	 		}
	 		else{
	 			console.log(result);
	 		}
	 	}).fail(function(){
	 		console.log('server error');
	 	});
		return false;				
	});	
});
JS;

$this->registerJs($js);
?>