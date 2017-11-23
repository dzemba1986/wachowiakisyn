<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
 * @var yii\web\View $this
 * @var app\models\Modyfication $modelTask
 * @var yii\widgets\ActiveForm $form
 */
?>
	
<?php $form = ActiveForm::begin([
	'id' => $task->formName()
]); ?>

    <?= $form->field($task, 'status')->dropDownList([1 => 'Wykonane', 0 => 'Niewykonane'], ['prompt' => 'Status']) ?>
    
    <?= $form->field($task, 'close_description')->textarea(['rows' => '4', 'maxlength' => 1000, 'style' => 'resize: vertical']) ?>
    
    <div class="form-group">
        <?= Html::submitButton('Zamknij', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$(function(){
	$('#{$task->formName()}').on('beforeSubmit', function(e){

	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result){
			
	 		if(result == 1){
	 			$(this).trigger('reset');
				$('#modal-task').modal('hide');
	 			$.pjax.reload({container:'#task-grid-pjax'});
	 		}
	 		else{
			
	 			alert(result);
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