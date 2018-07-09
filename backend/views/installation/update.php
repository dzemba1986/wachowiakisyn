<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\bootstrap\Modal;
use backend\models\Connection;
/* @var $this yii\web\View */
/* @var $modelConnection backend\models\Connection */

//echo '<center><h4>'.$modelConnection->modelAddress->fullAddress.'</h4></center>';
?>
<div class="connection-update">

    	<?php $form = ActiveForm::begin([
            'id'=>$modelInstallation->formName()
    	])?>
        
        <div style="display: flex">
		    
		    <?= $form->field($modelInstallation, 'wire_date', [
				'options' => ['class' => 'col-sm-8', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			])->widget(DatePicker::className(), [
            	'model' => $modelInstallation,
                'attribute' => 'wire_date',
				'pickerButton' => false,
				'language' => 'pl',
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d',
                ]
            ])?>
		    
		    <?= $form->field($modelInstallation, 'wire_length', [
		    	'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px; padding-right: 0px;'],
		    ]) ?>
		
		</div>
		
		<div style="display: flex">
		
			<?= $form->field($modelInstallation, 'wire_user', [
				'options' => ['class' => 'col-sm-12', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			]) ?>
		
		</div>	
		
		<div style="display: flex">
            
            <?= $form->field($modelInstallation, 'socket_date', [
				'options' => ['class' => 'col-sm-8', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			])->widget(DatePicker::className(), [
            	'model' => $modelInstallation,
                'attribute' => 'socket_date',
				'pickerButton' => false,
				'language' => 'pl',
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d',
                ]
            ])?>
            
		</div>
		
		<div style="display: flex">
		
			<?= $form->field($modelInstallation, 'socket_user', [
				'options' => ['class' => 'col-sm-12', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			]) ?>
			
		</div>
        
        <?= Html::submitButton($modelInstallation->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
        
        <?php ActiveForm::end() ?>
        
	
</div>

<script>

$(function(){
	
	$("#<?= $modelInstallation->formName(); ?>").on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr("action"), // serialize Yii2 form
	  		form.serialize()
	 	).done(function(result){
			
//	 		console.log(result);
	 		if(result == 1){
	 			//$(form).trigger('reset');
				$('#modal-installation-update').modal('hide');
	 			$.pjax.reload({container: '#installation-grid-pjax'});
	 		}
	 		else{
			
	 			$('#message').html(result);
	 		}
	 	}).fail(function(){
	 		console.log('server error');
	 	});
		return false;				
	});
	
})

</script>