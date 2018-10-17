<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
	'id' => $modelVlan->formName(),
])?>
    <div class="row">
    <?= $form->field($modelVlan, 'id', [
    	'options' => ['class' => 'col-md-4', 'style' => 'padding-right: 5px;']
    ]) ?>
    
    <?= $form->field($modelVlan, 'desc', [
    		'options' => ['class' => 'col-md-8', 'style' => 'padding-left: 5px;']
    		
    ]) ?>
    </div>
    
    <div class="form-group">
	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>
	</div>
	
<?php ActiveForm::end() ?>

<script>

$('#<?= $modelVlan->formName(); ?>').on('beforeSubmit', function(e){

	var form = $(this);
 	$.post(
  		form.attr("action"), // serialize Yii2 form
  		form.serialize()
 	).done(function(result){
		
// 		console.log(result);
 		if(result == 1){
 			$(form).trigger('reset');
			$('#modal-update-net').modal('hide');
 			$.pjax.reload({container: '#vlan-grid-pjax'});
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

