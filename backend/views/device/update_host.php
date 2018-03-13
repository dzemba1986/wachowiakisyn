<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\Address $address
 * @var backend\models\Device $device
 */

$form = ActiveForm::begin([
	'id' => $device->formName(),
])?>
	
	<div class="col-md-4">
	
     	<div class="row">
     	
    		<?= $form->field($device, 'proper_name', [
    			'options' => ['class' => 'col-sm-13', 'style' => 'padding-left: 0px; padding-right: 0px;']
    		]) ?>
				
		</div>
    
    	<div class="row">
    	
    		<?= $form->field($device, 'desc', [
    			'options' => ['class' => 'col-sm-13', 'style' => 'padding-left: 0px; padding-right: 0px;']
    		])->textarea() ?>	
    
            <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
            
  		</div>
	
	</div>
	
	<div class="col-md-2">
		
		<?= Html::label('Opcje :', null, ['hidden' => !$device->status]) ?>
	
		<?= $form->field($device, 'dhcp', [
		    'template' => "{label}{input}\n{hint}\n{error}",
		    'options' => ['hidden' => !$device->status]
		])->checkbox(['label' => 'DHCP']) ?>
		
		<?= $form->field($device, 'smtp', [
		    'template' => "{label}{input}\n{hint}\n{error}",
		    'options' => ['hidden' => !$device->status]
		])->checkbox(['label' => 'SMTP']) ?>
		
	</div>
	
<?php ActiveForm::end() ?>

<?php
$js = <<<JS

$(function() {

    $('#{$device->formName()}').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
    		
     		if(result == 1){
     			$("#device_tree").jstree(true).refresh();
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