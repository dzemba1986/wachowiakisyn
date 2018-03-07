<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** 
 * @var yii\web\View $this
 * @var backend\models\Swith $device
 */ 
?>

<div class="add-store-form">

    <?php $form = ActiveForm::begin([
    	'id' => $device->formName(),
    	'validationUrl' => Url::to(['virtual/validation'])	
    ]); ?>    
    
    <?= $form->field($device, 'mac', ['enableAjaxValidation' => true])?>
    
    <?= $form->field($device, 'desc')->textarea()?>
    
    <div class="form-group">
        <?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>
    </div>
       
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$(function() {
	$('#{$device->formName()}').on('beforeSubmit', function(e){
	
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
	 			$(form).trigger('reset');
				$('#modal-store').modal('hide');
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
JS;

$this->registerJs($js);
?>