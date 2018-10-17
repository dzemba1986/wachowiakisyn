<?php

use backend\modules\address\models\Address;
use backend\modules\address\models\AddressShort;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use common\models\seu\devices\OpticalAmplifier;
use common\models\seu\devices\OpticalSplitter;
use common\models\seu\devices\OpticalTransmitter;

/**
 * @var yii\web\View $this
 * @var backend\models\Radio $device
 * @var backend\models\Tree $link
 * @var backend\models\Address $address
 * @var backend\models\Ip $ip
 */ 

$form = ActiveForm::begin([
	'id' => 'add-device-form',
    ])?>
    
    <?= Html::label('Lokalizacja') ?>
    
    <div class="row">
    
    <?= $form->field($address, 't_ulica', [
			'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 3px;'],
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
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 3px; padding-right: 3px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->textInput(['placeholder' => $address->getAttributeLabel('dom')]) 
    ?>
    
    <?= $form->field($address, 'dom_szczegol' , [
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 3px; padding-right: 3px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->textInput(['placeholder' => $address->getAttributeLabel('dom_szczegol')]) 
    ?>
    
    <?= $form->field($address, 'pietro' , [
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 3px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList(Address::getFloor(), ['prompt' => $address->getAttributeLabel('pietro')]) 
    ?>
    
    </div>
    
    <?= Html::label('Urządzenie i porty') ?>
    
    <div class="row">

    <?= $form->field($link, 'parent_device', [
    		'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 3px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->widget(Select2::classname(), [
    		'language' => 'pl',
            'options' => [
            	'placeholder' => 'Urządzenie nadrzędne',
                'onchange' => new JsExpression("
                    $.get('" . Url::to(['link/list-port']) . "&deviceId=' + $('select#link-parent_device').val(), function(data){
                            $('select#link-parent_port').html(data);
					});
                                        		
                    $.get('" . Url::to(['link/list-port']) . "&deviceId=' + {$device->id} + '&mode=all', function(data){
						$('select#link-port').html(data);
					} );
                ")
            ],
    		'pluginOptions' => [
    			'allowClear' => true,
    			'minimumInputLength' => 1,
    			'language' => [
    				'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
    			],
    		    'ajax' => [
    		        'url' => Url::to(['device/list-from-tree']),
    		        'dataType' => 'json',
    		        'data' => new JsExpression("function(params) {
    					return {
    						q : params.term,
                            type : " . json_encode([OpticalAmplifier::TYPE, OpticalSplitter::TYPE, OpticalTransmitter::TYPE]) . "
    					};
    				}")
    		    ],
	    		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
	    		'templateResult' => new JsExpression('function(device) { return device.concat; }'),
	    		'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
    		]
    	])     
    ?>
    
    <?= $form->field($link, 'parent_port', [
    		'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 3px; padding-right: 3px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList([''], ['prompt' => $link->getAttributeLabel('parent_port')]) 
    ?>
    
    <?= $form->field($link, 'port', [
    		'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 3px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList([''], ['prompt' => $link->getAttributeLabel('port')]) 
    ?>
        
    </div>
    
	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>

<?php
$js = <<<JS
$(function(){
    $('.modal-header h4').html('Dodaj wzmacniacz optyczny');

	$("#add-device-form").on('beforeSubmit', function(e){
		var form = $(this);
     	$.post(
      		form.attr("action"),
      		form.serialize()
     	).done(function(result){
 			if(result == 1){
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