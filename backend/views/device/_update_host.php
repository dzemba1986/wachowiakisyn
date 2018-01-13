<?php

use backend\models\AddressShort;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
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
	
	<div class="col-md-6">
	
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
    	    
    	    <?= $form->field($address, 'lokal' , [
    	    		'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 3px; padding-right: 0px;'],
    	    		'template' => "{input}\n{hint}\n{error}",
    	    	])->textInput(['placeholder' => $address->getAttributeLabel('lokal')]) 
    	    ?>
	    
     	</div>
     	
     	<div class="row">
     	
         	<?= $form->field($address, 'lokal_szczegol' , [
    	    		'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 0px; padding-right: 3px;'],
    	    	])
    	    ?>
         	
    		<?= $form->field($device, 'proper_name', [
    			'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 3px; padding-right: 0px;']
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
		
		<?= Html::label('Opcje :') ?>
	
		<?= $form->field($device, 'distribution', [
			'template' => "{label}{input}\n{hint}\n{error}",
		])->checkbox(['label' => 'Szkieletowy']) ?>
		
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