<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
	'id' => $modelSubnet->formName(),
])?>

    <?= $form->field($modelSubnet, 'ip') ?>
    
    <?= $form->field($modelSubnet, 'desc') ?>

    <?= $form->field($modelSubnet, 'dhcp')->checkbox() ?>
    
    <div class="form-group">
	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>
	</div>
	
<?php ActiveForm::end() ?>

<script>

$('#<?= $modelSubnet->formName(); ?>').on('beforeSubmit', function(e){

	var form = $(this);
 	$.post(
  		form.attr("action"), // serialize Yii2 form
  		form.serialize()
 	).done(function(result){
		
// 		console.log(result);
 		if(result == 1){
 			$(form).trigger('reset');
			$('#modal-update-net').modal('hide');
 			$.pjax.reload({container: '#subnet-grid-pjax'});
 		}
 		else{
		
 			$('#message').html(result);
 		}
 	}).fail(function(){
 		console.log('server error');
 	});
	return false;				
});

</script>

