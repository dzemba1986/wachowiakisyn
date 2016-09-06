<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<div class="device-update">

	<?php $form = ActiveForm::begin([
		'id' => 'update-device-form',
		'enableAjaxValidation' => true,
		'validationUrl' => Url::toRoute(['swith/validation', 'id' => $modelDevice->id])
    ])?>
    
    <?= $form->field($modelDevice, 'serial') ?>
    
	<?= $form->field($modelDevice, 'mac') ?>
	
    <?= $form->field($modelDevice, 'desc')->textarea()?>
	
    
	<?= Html::submitButton($modelDevice->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>

</div>

<script>

$('#update-device-form').on('beforeSubmit', function(e){

	var form = $(this);
 	$.post(
  		form.attr("action"), // serialize Yii2 form
  		form.serialize()
 	).done(function(result){
		
// 		console.log(result);
 		if(result == 1){
 			$(form).trigger('reset');
			$('#modal-update-store').modal('hide');
 			$.pjax.reload({container: '#store-grid-pjax'});
 		}
 		else{
 			alert(result);
 		}
 	}).fail(function(){
 		console.log('server error');
 	});
	return false;				
});

</script>