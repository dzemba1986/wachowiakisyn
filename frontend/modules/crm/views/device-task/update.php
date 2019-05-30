<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\crm\DeviceTask;

/**
 * @var yii\web\View $this
 * @var common\models\crm\DeviceTask $task
 * @var yii\widgets\ActiveForm $form
 */
?>
	
<?php $form = ActiveForm::begin(['id'=>$task->formName()]); ?>
	
	<?= $form->field($task, 'category_id')->dropDownList(DeviceTask::$categoryName) ?>
	
	<div class="form-group">
        <?= Html::submitButton('Edytuj', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$(function(){
    $( '#modal-sm-title' ).html('Zmień kategorię');

	$('#{$task->formName()}').on('beforeSubmit', function(e){

	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result){
			
	 		if(result == 1){
	 			$(this).trigger('reset');
				$('#modal-sm').modal('hide');
	 			$.pjax.reload({container:'#task-grid-pjax'});
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