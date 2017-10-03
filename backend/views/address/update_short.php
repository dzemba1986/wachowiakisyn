<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Address;

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
	<?= $form->field($model, 'ulica') ?>

    <div class="row">
    
    <?= $form->field($model, 'ulica_prefix', [
    	'options' => ['class' => 'col-md-4', 'style' => 'padding-right: 5px;']
    ]) ?>

    <?= $form->field($model, 'name', [
    	'options' => ['class' => 'col-md-4', 'style' => 'padding-left: 5px; padding-right: 5px;']    		
    ]) ?>
    
	</div>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Edytuj', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$(function(){

	$("#{$model->formName()}").on('beforeSubmit', function(e){
		
		var form = $(this);
	 	
		$.post(
	  		form.attr("action"),	//url
	  		form.serialize()	//dane
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