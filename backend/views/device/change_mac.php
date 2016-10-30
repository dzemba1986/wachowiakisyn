<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


$change = '
exit' . '
copy r s' . '
y' . '
';


$form = ActiveForm::begin([
	'id' => $modelDevice->formName(),
	//'enableClientValidation'=>true,
])?>
	
	<?= $form->field($modelDevice, 'mac', []) ?>

    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary', 'data-clipboard-text' => $change]) ?>
    
<?php ActiveForm::end() ?>

<script>

$(function() {

	
	
    $('#<?= $modelDevice->formName(); ?>').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
    		
//     		console.log(result);
     		if(result == 1){
//      			$("#device_tree").jstree(true).refresh();
    			$('#modal-change-mac').modal('hide');
//      			$.pjax.reload({container: '#device-desc-pjax'});
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
</script>

