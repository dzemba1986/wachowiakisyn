<?php

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
$urlView = Url::to(['device/tabs-view']);

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
				$('#modal-device-sm').modal('hide');
                var ids = $("#device_tree").jstree('get_selected');
                if (ids.length == 1) {
                    var id = ids[0];
        			$('#device_desc').load('{$urlView}&id=' + id.substr(0, id.indexOf('.')));
                }
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