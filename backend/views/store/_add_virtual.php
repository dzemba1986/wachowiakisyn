<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Model;
use yii\helpers\Url;
use backend\models\Virtual;

/* @var $this yii\web\View */
/* @var $model backend\models\Installation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="add-store-form">

    <?php $form = ActiveForm::begin([
    	'id' => $modelDevice->formName(),
    	'validationUrl' => Url::toRoute(['virtual/validation'])	
    		
    ]); ?>    
    
    <?= $form->field($modelDevice, 'mac', 
        [       
            'enableAjaxValidation' => true, 
        ]
    )?>
    
    <?= $form->field($modelDevice, 'desc')->textarea()?>
    
    <div class="form-group">
        <?= Html::submitButton($modelDevice->isNewRecord ? 'Dodaj' : 'Update', ['class' => $modelDevice->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
       
    <?php ActiveForm::end(); ?>

</div>

<script>

$(function() {

	$('#<?= $modelDevice->formName(); ?>').on('beforeSubmit', function(e){
	
		var form = $(this);
	 	$.post(
	  		form.attr("action"), // serialize Yii2 form
	  		form.serialize()
	 	).done(function(result){
			
	// 		console.log(result);
	 		if(result == 1){
	 			$(form).trigger('reset');
				$('#modal-add-store').modal('hide');
	 			$.pjax.reload({container: '#store-grid-pjax'});
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