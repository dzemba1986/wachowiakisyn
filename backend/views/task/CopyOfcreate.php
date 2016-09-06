<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use backend\models\TaskCategory;
use backend\models\TaskType;
use backend\models\Address;
use backend\models\Connection;
use kartik\date\DatePicker;
use backend\controllers\ConnectionController;

/**
 * @var yii\web\View $this
 * @var app\models\Modyfication $modelTask
 * @var yii\widgets\ActiveForm $form
 */

//if ($modelTask->isNewRecord){
//	$modelTask->dateTo = $dateTime->format('Y-m-d');
//	$modelTask->dateFrom = $dateTime->format('Y-m-d');
//	$modelTask->timeFrom = $dateTime->format('H:i');
//	$modelTask->timeTo = $dateTime->add(new DateInterval('PT1H'))->format('H:i');
//}
//else {
//	$modelTask->dateTo = date('Y-m-d', strtotime($modelTask->end));
//	$modelTask->dateFrom = date('Y-m-d', strtotime($modelTask->start));
//	$modelTask->timeFrom = date('H:i', strtotime($modelTask->start));
//	$modelTask->timeTo = date('H:i', strtotime($modelTask->end));
//}
?>

<?php 
if (!is_null($conId)){ //jeżeli tworzymy zadanie z LP
	
	$modelConnetcion = Connection::findOne($conId);
	
	Html::label($modelConnetcion->modelAddress->fullAddress);
}
	
?> 




<h4><center><?= $modelTask->isNewRecord ? null  : $modelTask->title ?></center></h4>
	
<h4><center><?= ($modelTask->isNewRecord && isset($connectionId)) ? Connection::findOne($connectionId)->modelAddress->fullAddress  : null ?></center></h4>

<?php $form = ActiveForm::begin(['id'=>$modelTask->formName()]); ?>
    
    <?php 
        if ($modelTask->isNewRecord && !isset($connectionId))

        echo $this->render('_formAddress', [
                    'form' => $form,
                ]);
        
    ?>
    
    
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
						
	<div style="float:left; width:47%">					
		<?= $form->field($modelTask, 'start_time', ['template' => "{input}\n{hint}\n{error}"])->dropDownList(
			['08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00', '11:00' => '11:00', '12:00' => '12:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00', '16:00' => '16:00', '17:00' => '17:00', '18:00' => '18:00']) ?>	
	</div>
	
	<div style="float:right; width:47%">					
		<?= $form->field($modelTask, 'end_time', ['template' => "{input}\n{hint}\n{error}"])->dropDownList(
			['08:00' => '08:00', '09:00' => '09:00', '10:00' => '10:00', '11:00' => '11:00', '12:00' => '12:00', '13:00' => '13:00', '14:00' => '14:00', '15:00' => '15:00', '16:00' => '16:00', '17:00' => '17:00', '18:00' => '18:00']) ?>					
	</div>
	
	<div style="float:left; width:47%">
    	<?= $form->field($modelTask, 'type')->dropDownList(ArrayHelper::map(TaskType::find()->all(), 'id', 'name')) ?>
    </div>
    
    <div style="float:right; width:47%">
    	<?= $form->field($modelTask, 'category')->dropDownList(ArrayHelper::map(TaskCategory::find()->all(), 'id', 'name')) ?>
    </div>
    
    <div style="float:left; width:100%">
    <?= $form->field($modelTask, 'description')->textarea(['maxlength' => 1000]) ?>
    </div>
    <?= $form->field($modelTask, 'all_day')->checkbox() ?> 
        
    <div class="form-group">
        <?= Html::submitButton($modelTask->isNewRecord ? 'Dodaj' : 'Edytuj', ['class' => $modelTask->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
			//$(\$form).trigger('hide');
			$('#modal_task').modal('hide');
 			//$.pjax.reload({container:'#task-calendar'});
			window.location.replace('<?= Url::toRoute('task/view-calendar') ?>');
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