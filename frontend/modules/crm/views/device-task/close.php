<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/**
 * @var yii\web\View $this
 * @var common\models\crm\DeviceTask $task
 * @var yii\widgets\ActiveForm $form
 */
?>
	
<?php $form = ActiveForm::begin([
	'id' => $task->formName()
]); ?>

    <?= $form->field($task, 'close_desc')->textarea(['rows' => '4', 'maxlength' => 1000, 'style' => 'resize: vertical'])->label('Co wykonano?') ?>
    
    <div class="form-group">
        <?= Html::submitButton('Zamknij', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$(function(){
    $('#modal-sm-title').html('Zamkanie zgłoszenia');

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