<?php

use backend\models\Address;
use backend\models\AddressShort;
use backend\modules\task\models\InstallTask;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\time\TimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\task\models\TaskType;
use backend\modules\task\models\TaskCategory;
use backend\models\Connection;

/**
 * @var yii\web\View $this
 * @var backend\modules\task\models\InstallTask $task
 * $var backend\models\Address $
 * $var backend\models\Connection $connection
 * @var yii\widgets\ActiveForm $form
 */

?>

<?php $form = ActiveForm::begin(['id' => $task->formName()]); ?>

	<?php if (is_null($connection)) :?>
	
		<?= $form->field($address, 't_ulica')->label('Adres')->widget(Select2::className(),[
				'data' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
				'options' => ['placeholder' => 'Ulica'],
			]) 
		?>

		<div class="row">
	    
		    <?= $form->field($address, 'dom', [
		    	'template' => "{input}\n{hint}\n{error}",	    		
		    	'options' => ['class' => 'col-md-4', 'style' => 'padding-right: 5px;']
		    ])->textInput(['placeholder' => $address->getAttributeLabel('dom')]) ?>
		
			<?= $form->field($address, 'lokal', [
		    	'template' => "{input}\n{hint}\n{error}",
		    	'options' => ['class' => 'col-md-4', 'style' => 'padding-left: 5px; padding-right: 5px;']
		    ])->textInput(['placeholder' => $address->getAttributeLabel('lokal')]) ?>
			
		    <?= $form->field($address, 'dom_szczegol', [
		    	'template' => "{input}\n{hint}\n{error}",
		    	'options' => ['class' => 'col-md-4', 'style' => 'padding-left: 5px;']    		
		    ])->textInput(['placeholder' => $address->getAttributeLabel('dom_szczegol')]) ?>
	    
		</div>
	
	<?php else :?>
		
		<?= '<center><h4>'.Html::label($connection->modelAddress->toString()).'</h4></center>'; ?>
		
	<?php endif;?>


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
		])->dropDownList(ArrayHelper::map(TaskType::findWhereType(1)->all(), 'id', 'name'), ['prompt' => 'Wybierz...']) ?>
	
	<?= $form->field($task, 'category_id', [
			'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
		])->dropDownList(ArrayHelper::map(TaskCategory::findWhereType(1)->all(), 'id', 'name'), ['prompt' => 'Wybierz...']) ?>
	
	</div>
	
	<div class="row">
	
	<?= $form->field($task, 'phone', ['options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']]) ?>
	
	<?= $form->field($task, 'paid_psm')->checkbox() ?>
	
	</div>
	
	<?= $form->field($task, 'description')->textarea(['rows' => '4', 'style' => 'resize: vertical', 'maxlength' => 1000, 'placeholder' => 'Dodaj przybliżony koszt']) ?>
	
	<div class="form-group">
        <?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>
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