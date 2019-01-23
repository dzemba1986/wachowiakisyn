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
    <p>Na przełączniku opis portu to <b><?= $host->configParent->getSnmpDesc() ?></b>. Czy na pewno wysłać konfigurację na przełącznik? Potrwa to około 10s.</p>
    
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
                $.notify('Wysłano konfigurację - ustawiono vlan ' + result[1] + '.', {
                    type : 'success',
                    placement : { from : 'top', align : 'right'},
                });
	 		}
	 		else {
                $.notify('Niepowodzenie wysłania konfiguracji.', {
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