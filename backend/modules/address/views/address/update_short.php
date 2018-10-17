<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View 
 * @var $model backend\models\Address
 * @var $form yii\widgets\ActiveForm
 */ 
?>

<?php $form = ActiveForm::begin([
		'id'=>$model->formName()
	]
); ?>

    <?= $form->field($model, 'name') ?>
    
    <div class="form-group">
        <?= Html::submitButton('Edytuj', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$(function(){
    $('.modal-header h4').html('Edycja ulicy');

	$("#{$model->formName()}").on('beforeSubmit', function(e){
		
		var form = $(this);
	 	
		$.post(
	  		form.attr("action"),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
				$('#modal-update').modal('hide');
	 			$.pjax.reload({container: '#address-grid-pjax'});
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