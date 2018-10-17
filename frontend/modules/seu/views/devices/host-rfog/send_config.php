<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\base\View $this
 * @var backend\models\Host $host
 */

ActiveForm::begin([
	'id' => 'send-config'
]) ?>
    <p>Na przełączniku opis portu to <?= $host->parent->snmpDesc() ?>. Czy na pewno wysłać konfigurację na przełącznik?</p>
    
    <?= Html::submitButton('Tak', ['class' => 'btn btn-success']) ?>
  
    <?= Html::button('Nie', ['class' => 'no btn btn-danger']) ?>

<?php ActiveForm::end() ?>

<?php
$js = <<<JS
$(function() {

    $('.modal-header h4').html('Wyślij konfigurację');
        
    $('.no').click(function() {
		$('#modal-sm').modal('hide');
    });

    $('#send-config').on('beforeSubmit', function(e) {
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result) {
	 		if(result[0] == 1) {
				$('#modal-sm').modal('hide');
                $.growl.notice({ message: 'Wysłano konfigurację - ustawiono vlan ' + result[1]});
	 		}
	 		else {
	 			$.growl.error({ message: 'Nie wysłano konfiguracji'});
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