<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Address;
use backend\models\Subnet;

$form = ActiveForm::begin([
	'id' => $modelDevice->formName(),
	//'enableClientValidation'=>true,
])?>
	
	<div class="col-md-6">
	
		
	    <?= Html::label('Lokalizacja') ?>
	    
	    <div style="display: flex">
	    
	    <?= $form->field($modelAddress, 'ulica', [
				'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 0px; padding-right: 3px;'],
	    		'template' => "{input}\n{hint}\n{error}",
	    	])->widget(Select2::className(), [
	     		'data' => ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica'),
	       		'options' => ['placeholder' => 'Ulica'],
	       		'pluginOptions' => [
	            	'allowClear' => true
	            ],
	        ])
	    ?>
	    
	    <?= $form->field($modelAddress, 'dom' , [
	    		'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 0px; padding-right: 3px;'],
	    		'template' => "{input}\n{hint}\n{error}",
	    	])->textInput(['placeholder' => $modelAddress->getAttributeLabel('dom')]) 
	    ?>
	    
	    <?= $form->field($modelAddress, 'dom_szczegol' , [
	    		'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 0px; padding-right: 3px;'],
	    		'template' => "{input}\n{hint}\n{error}",
	    	])->textInput(['placeholder' => $modelAddress->getAttributeLabel('dom_szczegol')]) 
	    ?>
	    
	    <?= $form->field($modelAddress, 'pietro' , [
	    		'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 0px; padding-right: 0px;'],
	    		'template' => "{input}\n{hint}\n{error}",
	    	])->dropDownList(Address::getFloor(), ['prompt' => $modelAddress->getAttributeLabel('pietro')]) 
	    ?>
     	</div>
    
    	<div style="display: flex">
		<?= $form->field($modelDevice, 'mac', [
			'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;']
		]) ?>
		
		<?= $form->field($modelDevice, 'serial', [
			'options' => ['class' => 'col-sm-8', 'style' => 'padding-left: 0px; padding-right: 0px;']
		]) ?>
		</div>
		
		<div style="display: flex">
		<?= $form->field($modelDevice, 'name', [
			'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 0px; padding-right: 3px;']
		]) ?>
		
		<?= $form->field($modelDevice, 'original_name', [
			'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			//'template' => "{input}\n{hint}\n{error}",
		])->checkbox() ?>
		
		<?= $form->field($modelDevice, 'distribution', [
			'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 0px; padding-right: 0px;'],
			//'template' => "{input}\n{hint}\n{error}",
		])->checkbox(['label' => 'Szkielet']) ?>
		</div>
	
		<?= $form->field($modelDevice, 'desc', [
			'options' => ['class' => 'col-sm-13', 'style' => 'padding-left: 0px; padding-right: 0px;']
		])->textarea() ?>	

        <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
  
	
	</div>
	
<?php ActiveForm::end() ?>

<script>

$(function() {

    $('#<?= $modelDevice->formName(); ?>').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
    		
//     		console.log(result);
     		if(result == 1){
     			$("#device_tree").jstree(true).refresh();
//     			$('#modal-update-net').modal('hide');
//      			$.pjax.reload({container: '#subnet-grid-pjax'});
     		}
     		else{
    		
     			$('#message').html(result);
     		}
     	}).fail(function(){
     		console.log('server error');
     	});
    	return false;				
    });

    if($("#swith-original_name").is(':checked'))
    	$("#swith-name").attr('disabled', true); 
    else { //jeżeli nazwa orginalna nie jest zaznaczona
		var name = $("#swith-name").val()
        
    	$("#swith-name").val(name.replace(/^([\w|\W]{1,})([\[]{1})([\w|\W]{0,})([\]]{1})$/gi, "$3"));
    } 
    
    $("#swith-original_name").change(function() {
        $("#swith-name").attr('disabled', this.checked);
    });
});
</script>

