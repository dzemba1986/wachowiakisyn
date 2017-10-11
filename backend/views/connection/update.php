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
use yii\web\View;

/**
 * @var View $this
 * @var $model backend\models\Connection
 */

//echo '<center><h4>'.$model->modelAddress->fullAddress.'</h4></center>';
?>
<div class="connection-update">

    	<?php $form = ActiveForm::begin([
            'id'=>$model->formName(),
    	])?>
        
        <div style="display: flex">
		    
		    <?= $form->field($model, 'phone', [
		    	'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;'],
		    ]) ?>
		    
		    <?= $form->field($model, 'phone2', [
		    	'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px; padding-right: 0px;'],
		    ]) ?>
		    
		    <?php if(!$model->host && $model->wire >= 1 && $model->socket >= 1) :?>
		    
		    <?= Html::a('Czysc instalacje', Url::to(['installation/crash', 'connectionId' => $model->id]), ['class' => 'crash-installation']); ?>
		
			<?php endif; ?>
		</div>
		
		<div style="display: flex">
            
            <?= $form->field($model, 'pay_date', [
				'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			])->widget(DatePicker::className(), [
            	'model' => $model,
                'attribute' => 'pay_date',
                'language'=>'pl',
				'removeButton' => FALSE,
				'disabled' => $model->socket > 0 ? false : true,
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d', //wybór daty max do dziś
                ]
            ])?>
            
            <?= $form->field($model, 'close_date', [
				'options' => ['class' => 'col-sm-4', 'style' => 'color : red; padding-left: 3px; padding-right: 0px;'],
			])->widget(DatePicker::className(), [
            	'model' => $model,
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
		
			<?= $form->field($model, 'device', [
    			'options' => ['class' => 'col-sm-8', 'style' => 'padding-left: 0px; padding-right: 3px;'],
    		])->widget(Select2::classname(), [
    			//'initValueText' => 'OP 120/ - OP120 - [172.20.4.44]', //$concatInit,
    			'language' => 'pl',
            	'options' => [
            		//'id' => 'select2-connection-update',	
            		
            		'onchange' => new JsExpression("

						$.get('" . Url::toRoute('tree/select-list-port') . "&device=' + $(this).val() + '&mode=free', function(data){
							$('#connection-port').html(data).val('" . $model->port . "');
						});
					")
            	],
	    		'pluginOptions' => [
	    			'placeholder' => 'Urządzenie nadrzędne',
	    			'allowClear' => true,
	    			'minimumInputLength' => 1,
	    			'language' => [
	    				'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
	    			],
	    			'ajax' => [
	    				'url' => Url::toRoute('device/list'),
	    				'dataType' => 'json',
	    				'data' => new JsExpression("function(params) { return {
	    					q : params.term, 
	    					type : $model->type == 1 || $model->type == 3 ? [2] : [3],
    						distribution : $model->type == 1 ? false : null
						}; 
					}")
		    		],
		    		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		    		'templateResult' => new JsExpression('function(device) { return device.concat; }'),
		    		'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
	    		]
    		]) ?>
    		
    		<?php $port = isset($model->port) ? $model->port : null?>
    		
			<?= $form->field($model, 'port', [
				'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px; padding-right: 0px;'],
			])->dropDownList([$port], ['prompt'=>'port']) ?>
			
		</div>
		
		<?php if($model->type == 1 || $model->type == 3) :?>
		
		<div style="display: flex">
		
		<?= $form->field($model, 'mac', [
				'options' => ['class' => 'col-sm-5', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			]) ?>
			
		</div>
		
		<?php endif; ?>	
        
		<?= $form->field($model, 'info')->textarea(['style' => 'resize: vertical']) ?>
        
        <?= $form->field($model, 'info_boa')->textarea(['style' => 'resize: vertical']) ?>
        
        <?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
        
        <?php ActiveForm::end() ?>
        
	
</div>

<script>

$(function(){
	
	var device = <?= json_encode($model->device); ?>

	if (device){
		$.getJSON("<?= Url::toRoute(['device/list', 'id' => $model->device])?>", function(data){
			$('#select2-connection-device-container').html(data.results.concat);
		});
	
		$("#connection-device").trigger("change");
	}

	$(".modal-header h4").html("<?= $model->modelAddress->stringAddress() ?>");

	$("#<?= $model->formName(); ?>").on('beforeSubmit', function(e){
		
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

	$("body").on('click', '.crash-installation', function(event){
        
		$.get($(this).attr('href'), function(data) {

        	alert('Wyczyszczono !');
		});
    
        return false;
	});

// 	if (!($("#connection-device").val()) && !($("#connection-mac").val())){
// 		$(".add-to-tree").hide();
// 	}	
})

</script>