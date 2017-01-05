<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use backend\models\Connection;
use backend\models\Device;
/* @var $this yii\web\View */
/* @var $modelConnection backend\models\Connection */

//echo '<center><h4>'.$modelConnection->modelAddress->fullAddress.'</h4></center>';
?>
<div class="connection-update">

    	<?php $form = ActiveForm::begin([
            'id'=>$modelConnection->formName(),
    		'enableAjaxValidation' => true,
    		'validationUrl' => Url::toRoute('connection/validation')	
    	])?>
        
        <div style="display: flex">
		    
		    <?= $form->field($modelConnection, 'phone', [
		    	'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;'],
		    ]) ?>
		    
		    <?= $form->field($modelConnection, 'phone2', [
		    	'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px; padding-right: 0px;'],
		    ]) ?>
		
		</div>
		
		<div style="display: flex">
            
            <?= $form->field($modelConnection, 'pay_date', [
				'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			])->widget(DatePicker::className(), [
            	'model' => $modelConnection,
                'attribute' => 'pay_date',
                'language'=>'pl',
				'removeButton' => FALSE,
				'disabled' => $modelConnection->socket > 0 ? false : true,
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d', //wybór daty max do dziś
                ]
            ])?>
            
            <?= $form->field($modelConnection, 'close_date', [
				'options' => ['class' => 'col-sm-4', 'style' => 'color : red; padding-left: 3px; padding-right: 0px;'],
			])->widget(DatePicker::className(), [
            	'model' => $modelConnection,
                'attribute' => 'close_date',
                'language'=>'pl',
				'removeButton' => FALSE,
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d', //wybór daty max do dziś
                ]
            ])?>
		
		</div>
		
		<div style="display: flex">
		
			<?php $concatInit = empty($modelConnection->device) ? '' : Device::findOne($modelConnection->device)->modelAddress->fullDeviceAddress; ?>
			
			<?= $form->field($modelConnection, 'device', [
    			'options' => ['class' => 'col-sm-8', 'style' => 'padding-left: 0px; padding-right: 3px;'],
    		])->widget(Select2::classname(), [
    			//'initValueText' => 'OP 120/ - OP120 - [172.20.4.44]', //$concatInit,
    			'language' => 'pl',
            	'options' => [
            		//'id' => 'select2-connection-update',	
            		'placeholder' => 'Urządzenie nadrzędne',
            		'onchange' => new JsExpression("

						$.get('" . Url::toRoute('tree/select-list-port') . "&device=' + $(this).val() + '&type=free', function(data){
							$('#connection-port').html(data).val('" . $modelConnection->port . "');
						});
					")
            	],
	    		'pluginOptions' => [
	    			
	    			'allowClear' => true,
	    			'minimumInputLength' => 1,
	    			'language' => [
	    				'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
	    			],
	    			'ajax' => [
	    				'url' => Url::toRoute('device/list'),
	    				'dataType' => 'json',
	    				'data' => new JsExpression('function(params) { return {
	    					q:params.term, 
	    					type: [2, 8], 
	    					dist:false
						}; }')
		    		],
		    		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		    		'templateResult' => new JsExpression('function(device) { return device.concat; }'),
		    		'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
	    		]
    		]) ?>
    		
    		<?php $port = isset($modelConnection->port) ? $modelConnection->port : null?>
    		
			<?= $form->field($modelConnection, 'port', [
				'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px; padding-right: 0px;'],
			])->dropDownList([$port], ['prompt'=>'port']) ?>
			
		</div>
		
		<div style="display: flex">
		
		
		
		<?= $form->field($modelConnection, 'mac', [
				'options' => ['class' => 'col-sm-5', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			]) ?>
			
		</div>	
        
		<?= $form->field($modelConnection, 'info')->textarea(['style' => 'resize: vertical']) ?>
        
        <?= $form->field($modelConnection, 'info_boa')->textarea(['style' => 'resize: vertical']) ?>
        
        <?= Html::submitButton($modelConnection->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
        
        <?php ActiveForm::end() ?>
        
	
</div>

<script>

$(function(){
	
	var device = <?= json_encode($modelConnection->device); ?>

// 	if (device){
//		$.getJSON("<?= Url::toRoute(['device/list', 'id' => $modelConnection->device])?>", function(data){
// // 			$('#select2-connection-device-container').html(data.results.concat);
// // 		});
	
// 		$("#connection-device").trigger("change");
// 	}

	$(".modal-header h4").html("<?= $modelConnection->modelAddress->fullAddress ?>");

	$("#<?= $modelConnection->formName(); ?>").on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr("action"), // serialize Yii2 form
	  		form.serialize()
	 	).done(function(result){
			
//	 		console.log(result);
	 		if(result == 1){
	 			//$(form).trigger('reset');
				$('#modal-connection-update').modal('hide');
	 			$.pjax.reload({container: '#connection-grid-pjax'});
	 		}
	 		else{
			
	 			$('#message').html(result);
	 		}
	 	}).fail(function(){
	 		console.log('server error');
	 	});
		return false;				
	});

// 	if (!($("#connection-device").val()) && !($("#connection-mac").val())){
// 		$(".add-to-tree").hide();
// 	}	
})

</script>