<?php

use common\models\crm\TaskCategory;
use common\models\crm\TaskType;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\modules\task\models\InstallTask $task
 * @var yii\widgets\ActiveForm $form
 */
?>
	
<?php $form = ActiveForm::begin(['id'=>$task->formName()]); ?>

	<?= '<center><h4>'.Html::label($task->address->toString()).'</h4></center>'; ?>
   
	<?= $form->field($task, 'start_date')->label('Data i czas')->textInput()->widget(DatePicker::className(), [
			'type' => DatePicker::TYPE_COMPONENT_APPEND,
			'pluginOptions' => [
	        	'format' => 'yyyy-mm-dd',
	            'todayHighlight' => true,
	        ]
		]) ?>

    <div class="row">
	
	<?= $form->field($task, 'start_time', [
			'template' => "{input}\n{hint}\n{error}",
			'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
		])->widget(TimePicker::className(), [
			'pluginOptions' => [
				'minuteStep' => 60,
				'showMeridian' => false
			]		
		]) ?>
	
	<?= $form->field($task, 'end_time', [
			'template' => "{input}\n{hint}\n{error}",
			'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
		])->widget(TimePicker::className(), [
			'pluginOptions' => [
				'minuteStep' => 60,
				'showMeridian' => false
			]		
		]) ?>
		
	</div>	   
	
	<div class="row">
	
	<?= $form->field($task, 'type_id',[
			'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
		])->dropDownList(ArrayHelper::map(TaskType::find()->all(), 'id', 'name')) ?>
	
	<?= $form->field($task, 'category_id', [
			'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
		])->dropDownList(ArrayHelper::map(TaskCategory::find()->all(), 'id', 'name')) ?>
	</div>
	
	<div class="row">
	
	<?= $form->field($task, 'phone', ['options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']]) ?>
	
	<?= $form->field($task, 'paid_psm')->checkbox() ?>
	
	</div>
	
	<?= $form->field($task, 'description')->textarea(['rows' => '4', 'style' => 'resize: vertical', 'maxlength' => 1000]) ?>
	
	<div class="form-group">
        <?= Html::submitButton('Edytuj', ['class' => 'btn btn-primary']) ?>
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
				$('#calendar').fullCalendar('refetchEvents');
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