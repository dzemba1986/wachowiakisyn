<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
/**
 * @var yii\web\View $this
 * @var app\models\Modyfication $modelTask
 * @var yii\widgets\ActiveForm $form
 */
?>
	
<?php $form = ActiveForm::begin([
	'id' => $task->formName()
]); ?>

	<div class="row">

    <?= $form->field($task, 'installer', [
    		'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
    ])->dropDownList(User::getIstallers(), ['multiple' => true]) ?>
   
    <?= $form->field($task, 'cost', [
    		//'template' => "{input}\n{hint}\n{error}",
    		'options' => ['placeholder' => 'Koszt', 'class' => 'col-md-6', 'style' => 'padding-left: 5px;']
    ])->label('Koszt i status') ?>
    
    <?= $form->field($task, 'status', [
    		'template' => "{input}\n{hint}\n{error}",
    		'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
    ])->dropDownList([1 => 'Wykonane', 0 => 'Niewykonane'], ['prompt' => 'Status']) ?>
    
    </div>
    
    <?= $form->field($task, 'paid_psm')->checkbox() ?>
    
    <?= $form->field($task, 'description')->textarea(['rows' => '4', 'maxlength' => 1000, 'style' => 'resize: vertical']) ?>
    
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