<?php

use backend\modules\address\models\Teryt;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var integer $id
 * @var backend\models\Tree $link
 * @var backend\models\Address $address
 */ 

$form = ActiveForm::begin([
	'id' => 'add-device-form',
    ])?>
    
	<?= Html::label('Lokalizacja') ?>
	    
    <?= $form->field($address, 't_ulica', [
    		'template' => "{input}\n{hint}\n{error}",
    	])->widget(Select2::className(), [
     		'data' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
       		'options' => ['placeholder' => 'Ulica'],
       		'pluginOptions' => [
            	'allowClear' => true
            ],
        ]) ?>
	    
	<div class="row">
	
	    <?= $form->field($address, 'dom' , [
	    		'options' => ['class' => 'col-sm-4', 'style' => 'padding-right: 3px;'],
	    		'template' => "{input}\n{hint}\n{error}",
	    	])->textInput(['placeholder' => $address->getAttributeLabel('dom')]) ?>
	    
	    <?= $form->field($address, 'dom_szczegol' , [
	    		'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px; padding-right: 3px;'],
	    		'template' => "{input}\n{hint}\n{error}",
	    	])->textInput(['placeholder' => $address->getAttributeLabel('dom_szczegol')]) ?>
	    
	    <?= $form->field($address, 'lokal' , [
	    		'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px;'],
	    		'template' => "{input}\n{hint}\n{error}",
	       ])->textInput(['placeholder' => $address->getAttributeLabel('lokal')]) ?>
    	    
 	</div>
    
    <?= $form->field($link, 'parent_port')->dropDownList(['']) ?>
    
	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>

<?php
$urlPortList = Url::to(['link/list-port']);

$js = <<<JS
$(function(){

    $('.modal-header h4').html('Dodaj nieaktywnego hosta');

    $.get('{$urlPortList}&deviceId={$id}', function(data){
    	$("select[name='Link[parent_port]']").html(data);
    } );

	$("#add-device-form").on('beforeSubmit', function(e){
		var form = $(this);
     	$.post(
      		form.attr("action"),
      		form.serialize()
     	).done(function(result){
 			if(result == 1){
                var tree = $("#device_tree").jstree(true);

				$('#modal-sm').modal('hide');
                tree.refresh();
			}
 			else {
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