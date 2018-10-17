<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\Address $address
 * @var backend\models\Host $device
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
$urlView = Url::to(['tabs-view']);

$js = <<<JS
$(function() {

    $('#{$device->formName()}').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
    		if(result == 1){
     			$('#device_desc').load('{$urlView}&id=' + {$device->id});
                $.growl.notice({ message: 'Zaktualizowano hosta'});
     		}
     		else{
     			$.growl.error({ message: 'Błąd edycji hosta'});
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