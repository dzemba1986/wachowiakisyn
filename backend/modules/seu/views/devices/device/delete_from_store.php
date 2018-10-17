<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */

$form = ActiveForm::begin([
    'id' => 'delete-from-store'
]) ?>
    <p>Operacja usunie nieodwracalnie urządzenie. Czy na pewno?</p>
    
    <?= Html::submitButton('Tak', ['class' => 'btn btn-success']) ?>
  
    <?= Html::button('Nie', ['class' => 'no btn btn-danger']) ?>

<?php ActiveForm::end() ?>

<?php
$js = <<<JS
$(function(){

    $('.modal-header h4').html('Usuń urządzenie');
    
    $('.no').click(function() {
		$('#modal-sm').modal('hide');
    });
    
    $('#delete-from-store').on('beforeSubmit', function(e){
    
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
				$('#modal-sm').modal('hide');
                $.pjax.reload({container: '#store-grid-pjax'});
                $.growl.notice({ message: 'Usunięto urządzenie'});
	 		}
	 		else{
	 			$.growl.error({ message: 'Błąd usunięcia urządzenia'});
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