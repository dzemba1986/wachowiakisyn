<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\TaskCategory;
use backend\models\TaskType;
use kartik\date\DatePicker;
use kartik\time\TimePicker;

/**
 * @var yii\web\View $this
 * @var app\models\Modyfication $modelTask
 * @var yii\widgets\ActiveForm $form
 */
?>
	
<?php $form = ActiveForm::begin(['id'=>$modelTask->formName()]); ?>

	<?= '<center><h4>'.Html::label($modelTask->modelAddress->fullAddress).'</h4></center>'; ?>
   
	<?= $form->field($modelTask, 'start_date')->label('Data i czas')->textInput()->widget(DatePicker::className(), [
			'type' => DatePicker::TYPE_RANGE,
	        'form' => $form,
			'attribute2' => 'end_date', 
	    	'language' => 'pl',
	    	'size' => 'md',
			'pluginOptions' => [
	        	'format' => 'yyyy-mm-dd',
	            'todayHighlight' => true,
	        ]
		]) ?>

    <div class="row">
	
	<?= $form->field($modelTask, 'start_time', [
			'template' => "{input}\n{hint}\n{error}",
			'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
		])->widget(TimePicker::className(), [
			'pluginOptions' => [
				'minuteStep' => 30,
				'showMeridian' => false
			]		
		]) ?>
	
	<?= $form->field($modelTask, 'end_time', [
			'template' => "{input}\n{hint}\n{error}",
			'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
		])->widget(TimePicker::className(), [
			'pluginOptions' => [
				'minuteStep' => 30,
				'showMeridian' => false
			]		
		]) ?>
		
	</div>	   
	
	<div class="row">
	
	<?= $form->field($modelTask, 'type',[
			'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
		])->dropDownList(ArrayHelper::map(TaskType::find()->all(), 'id', 'name')) ?>
	
	<?= $form->field($modelTask, 'category', [
			'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
		])->dropDownList(ArrayHelper::map(TaskCategory::find()->all(), 'id', 'name')) ?>
	</div>
	
	<div class="row">
	
	<?= $form->field($modelTask, 'phone', ['options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']]) ?>
	
	<?= $form->field($modelTask, 'all_day', ['options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']])->checkbox() ?>
	
	</div>
	
	<?= $form->field($modelTask, 'description')->textarea([
			'style' => 'resize: vertical', 
			'maxlength' => 1000, 
			'placeholder' => 'Dodaj przybliżony koszt'
	]) ?>
	
	<div class="form-group">
        <?= Html::submitButton('Edytuj', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>



<script>

$('#<?= $modelTask->formName()?>').on('beforeSubmit', function(e){

 	$.post(
  		$(this).attr("action"), // serialize Yii2 form
  		$(this).serialize()
 	).done(function(result){
		
// 		console.log(result);
 		if(result == 1){
 			$($(this)).trigger('reset');
			$('#modal-create-task').modal('hide');
			$('#modal-open-calendar').modal('hide');
//  			$.pjax.reload({container:'#calendar-pjax'});
 		}
 		else{
		
 			$('#message').html(result);
 		}
 	}).fail(function(){
 		console.log('server error');
 	});
	return false;				
});

</script>