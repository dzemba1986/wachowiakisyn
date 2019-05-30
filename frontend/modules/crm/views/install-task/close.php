<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\crm\Task;
/**
 * @var yii\web\View $this
 * @var common\models\crm\InstallTask $task
 * @var yii\widgets\ActiveForm $form
 */
?>
	
<?php $form = ActiveForm::begin([
	'id' => $task->formName(),
    'options' => ['style' => 'padding-left:10px;padding-right:10px'],
]);

	echo Html::beginTag('div', ['class' => 'row no-gutter']);
	
	   echo $form->field($task, 'done_by', [
	       'options' => ['class' => 'col-md-6']
	   ])->dropDownList(User::getIstallers(), ['multiple' => true]);
	   
	   echo $form->field($task, 'pay_by', [
	       'options' => ['class' => 'col-md-6']
	   ])->dropDownList(Task::PAY_BY);

	   echo $form->field($task, 'cost', [
	       'options' => ['class' => 'col-md-3']
	   ])->textInput(['placeholder' => 'Koszt'])->label(false);

	   echo $form->field($task, 'fulfit', [
	       'options' => ['class' => 'col-md-3']
	   ])->dropDownList(['Tak', 'Nie'], ['prompt' => 'Wykonano'])->label(false);

    echo Html::endTag('div');
	
	echo Html::beginTag('div', ['class' => 'row no-gutter']);
        echo $form->field($task, 'close_desc')->textarea(['rows' => '4', 'maxlength' => 1000, 'style' => 'resize: vertical']);
        
        echo Html::submitButton('Zamknij', ['class' => 'btn btn-primary']);
    echo Html::endTag('div');

    ActiveForm::end(); 

$js = <<<JS
$(function() {
    $('#modal-sm-title').html('Zamykanie montażu');

	$('#{$task->formName()}').on('beforeSubmit', function(e) {
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