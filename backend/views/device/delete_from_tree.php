<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */

$form = ActiveForm::begin([
	'id' => 'to-store'
]) ?>
    <p>Operacja przeniesie urządzenie do magazynu i zwolni wszystkie adresy IP powiązane z urządzeniem. Czy na pewno?</p>
    
    <?= Html::submitButton('Tak', ['class' => 'btn btn-success']) ?>
  
    <?= Html::button('Nie', ['class' => 'no btn btn-danger']) ?>

<?php ActiveForm::end() ?>

<?php
$js = <<<JS
$(function(){

    $('.modal-header h4').html('Przenieś do magazynu');
        
    $('.no').click(function() {
        var tree = $("#device_tree").jstree(true);

		$('#modal-sm').modal('hide');
        tree.refresh();
    });

    $('#to-store').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
				$('#modal-sm').modal('hide');
                $('#device_desc').text('Usunięto urządzenie');
	 		}
	 		else{
	 			$('#message').html(result);
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