<?php

use backend\models\Address;
use backend\models\Teryt;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\Address $address
 * @var backend\models\OpticalTransmitter $device
 */

$form = ActiveForm::begin([
	'id' => $device->formName(),
    'validationUrl' => Url::to(['validation', 'id' => $device->id])
])?>
	
	<div class="col-md-5">
	
		<div class="row">
	    	<?= Html::label('Lokalizacja') ?>
	    </div>
	    
	    <div class="row">
    	    <?= $form->field($address, 't_ulica', [
    				'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 0px; padding-right: 3px;'],
    	    		'template' => "{input}\n{hint}\n{error}",
    	    	])->widget(Select2::className(), [
        			'data' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
    	       		'options' => ['placeholder' => 'Ulica'],
    	       		'pluginOptions' => [
    	            	'allowClear' => true
    	            ],
    	        ])
    	    ?>
    	    
    	    <?= $form->field($address, 'dom' , [
    	    		'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 3px; padding-right: 3px;'],
    	    		'template' => "{input}\n{hint}\n{error}",
    	    	])->textInput(['placeholder' => $address->getAttributeLabel('dom')]) 
    	    ?>
    	    
    	    <?= $form->field($address, 'dom_szczegol' , [
    	    		'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 3px; padding-right: 3px;'],
    	    		'template' => "{input}\n{hint}\n{error}",
    	    	])->textInput(['placeholder' => $address->getAttributeLabel('dom_szczegol')]) 
    	    ?>
    	    
    	    <?= $form->field($address, 'pietro' , [
    	    		'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 3px; padding-right: 0px;'],
    	    		'template' => "{input}\n{hint}\n{error}",
    	    	])->dropDownList(Address::getFloor(), ['prompt' => $address->getAttributeLabel('pietro')]) 
    	    ?>
     	</div>
    
    	<div class="row">
        	<?= $form->field($device, 'proper_name', [
        			'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;']
    		]) ?>
    		
    		<?= $form->field($device, 'serial', [
    		    'enableAjaxValidation' => true,
    			'options' => ['class' => 'col-sm-5', 'style' => 'padding-left: 3px; padding-right: 3px;']
    		]) ?>
    		
    		<?= $form->field($device, 'insertion_loss', [
    		    'template' => "{label} (dB)\n{input}\n{hint}\n{error}",
    			'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 3px; padding-right: 0px;']
    		]) ?>
		</div>
		
		<div class="row">
    		<?= $form->field($device, 'desc', [
    			'options' => ['class' => 'col-sm-12', 'style' => 'padding-left: 0px; padding-right: 0px;']
    		])->textarea() ?>
    		
    		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
		</div>	
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
                $.growl.notice({ message: 'Zaktualizowano nadajnik optyczny'});
     		}
     		else{
     			$.growl.error({ message: 'Błąd edycji nadajnika optycznego'});
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