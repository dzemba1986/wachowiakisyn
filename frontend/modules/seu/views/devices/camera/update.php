<?php

use backend\modules\address\models\Address;
use backend\modules\address\models\Teryt;
use kartik\growl\GrowlAsset;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\Address $address
 * @var backend\models\Device $device
 */

GrowlAsset::register($this);

$form = ActiveForm::begin([
	'id' => $device->formName(),
    'validationUrl' => Url::to(['validation', 'id' => $device->id])
])?>
	
	<div class="col-md-5">
	
		<div class="row no-gutter">
	    
	    	<?= Html::label('Lokalizacja') ?>
	    
	    </div>
	    
	    <div class="row no-gutter">
	    
    	    <?= $form->field($address, 't_ulica', [
    				'options' => ['class' => 'col-sm-6'],
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
    	    		'options' => ['class' => 'col-sm-2'],
    	    		'template' => "{input}\n{hint}\n{error}",
    	    	])->textInput(['placeholder' => $address->getAttributeLabel('dom')]) 
    	    ?>
    	    
    	    <?= $form->field($address, 'dom_szczegol' , [
    	    		'options' => ['class' => 'col-sm-2'],
    	    		'template' => "{input}\n{hint}\n{error}",
    	    	])->textInput(['placeholder' => $address->getAttributeLabel('dom_szczegol')]) 
    	    ?>
    	    
    	    <?= $form->field($address, 'pietro' , [
    	    		'options' => ['class' => 'col-sm-2'],
    	    		'template' => "{input}\n{hint}\n{error}",
    	    	])->dropDownList(Address::getFloor(), ['prompt' => $address->getAttributeLabel('pietro')]) 
    	    ?>
     	
     	</div>
    
		<div class="row no-gutter">
		
    		<?= $form->field($device, 'proper_name', [
    			'options' => ['class' => 'col-sm-3']
    		]) ?>
    		
    		<?= $form->field($device, 'alias', [
    			'options' => ['class' => 'col-sm-4']
    		]) ?>
		
			<?= $form->field($device, 'geolocation', [
    			'options' => ['class' => 'col-sm-5']
    		]) ?>
		</div>
	
		<div class="row no-gutter">
		
    		<?= $form->field($device, 'desc', [
    			'options' => ['class' => 'col-sm-13']
    		])->textarea() ?>	
    
            <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
        
  		</div>
	
	</div>
	
	<div class="col-md-2">
		
		<?= Html::label('Opcje :') ?>
	
		<?= $form->field($device, 'dhcp', [
		    'template' => "{label}{input}\n{hint}\n{error}",
		])->checkbox(['label' => 'DHCP']) ?>
		
		<?= $form->field($device, 'monitoring', [
			'template' => "{label}{input}\n{hint}\n{error}",
		])->checkbox() ?>
		
	</div>
	
<?php ActiveForm::end() ?>

<?php
$urlView = Url::to(['tabs-view', 'id' => $device->id]);

$js = <<<JS
$(function() {

    $('#{$device->formName()}').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
    		if(result == 1){
     			$('#device_desc').load('{$urlView}');
                $.notify('Zaktualizowano urządzenie.', {
                    type: 'success',
                    placement : { from : 'top', align : 'right'},
                });
     		}
     		else{
     			$.notify('Błąd aktualizacji urządzenia.', {
                    type: 'danger',
                    placement : { from : 'top', align : 'right'}, 
                });
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