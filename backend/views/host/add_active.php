<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 * @var integer $hostId Id Host
 */

$form = ActiveForm::begin([
	'id' => 'add-connection-active'
]) ?>
    <p><?= Html::label("Operacja doda umowę do hosta o id : {$hostId}") ?></p>
    
    <?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>

<?php
$js = <<<JS
$(function(){

    $('#add-connection-active').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		{}
	 	).done(function(result){
	 		if(result == 1){

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