<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var backend\models\Connection $model 
 */
?>

<div class="connection-update">

    <?php $form = ActiveForm::begin([
		'id'=>$model->formName(),
    ])?>
        
    <div class="col-sm-6">
    	<div class="row">
		    <?= $form->field($model, 'phone', [
	    		'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 0px; padding-right: 1px;'],
	    	]) ?>
	    
		    <?= $form->field($model, 'phone2', [
		    	'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 1px; padding-right: 2px;'],
		    ]) ?>
		</div>
			
		<div class="row">
	    	<?= $form->field($model, 'pay_date', [
				'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 0px; padding-right: 1px;'],
			])->widget(DatePicker::className(), [
            	'model' => $model,
                'attribute' => 'pay_date',
				'pickerButton' => FALSE,
				'disabled' => $model->socket > 0 ? false : true,
				'language' => 'pl',
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d',
                ]
            ])?>
            
            <?= $form->field($model, 'close_date', [
				'options' => ['class' => 'col-sm-6', 'style' => 'color : red; padding-left: 1px; padding-right: 2px;'],
			])->widget(DatePicker::className(), [
            	'model' => $model,
                'attribute' => 'close_date',
				'pickerButton' => false,
				'language' => 'pl',
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d',
                ]
            ])?>
		</div>
		
		<div class="row">
			<?= $form->field($model, 'info', [
				'options' => ['class' => 'col-sm-12', 'style' => 'padding-left: 0px; padding-right: 2px;'],
			])->textarea(['rows' => "7", 'style' => 'resize: vertical']) ?>
		</div>
    </div>
    
    <div class="col-md-6">
    	<div class="row">
		<?= $form->field($model, 'device', [
			'options' => ['class' => 'col-sm-12', 'style' => 'padding-left: 2px; padding-right: 0px;'],
    	])->widget(Select2::classname(), [
    		'language' => 'pl',
           	'options' => [
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
					};}")
		    	],
		    	'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		    	'templateResult' => new JsExpression('function(device) { return device.concat; }'),
		    	'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
	    	]
    	]) ?>
    	</div>	
    	<div class="row">
    		<?php $port = isset($model->port) ? $model->port : null?>
	    		
			<?= $form->field($model, 'port', [
				'options' => ['class' => 'col-sm-5', 'style' => 'padding-left: 2px; padding-right: 1px;'],
			])->dropDownList([$port], ['prompt'=>'port']) ?>
    	
    		<?php if($model->type == 1 || $model->type == 3) :?>
				<?= $form->field($model, 'mac', [
					'options' => ['class' => 'col-sm-7', 'style' => 'padding-left: 1px; padding-right: 0px;'],
				]) ?>
			<?php endif; ?>
		</div>
        
        <div class="row">
	        <?= $form->field($model, 'info_boa', [
	        	'options' => ['class' => 'col-sm-12', 'style' => 'padding-left: 2px; padding-right: 0px;'],
	        ])->textarea(['rows' => "7", 'style' => 'resize: vertical']) ?>
    	</div>
    </div>
    
    <?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
    
    <?php ActiveForm::end() ?>
</div>

<?php 
$deviceListUrl = Url::to(['device/list', 'id' => $model->device]);

$this->registerJs(
"$(function(){
	$('.modal-header h4').html('{$model->modelAddress->toString()}');

	$('#{$model->formName()}').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
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

	//wyświetlanie wybranego urzadzenia i portu
	var device = {$model->device}

	if (device){
		$.getJSON('{$deviceListUrl}', function(data){
			$('#select2-connection-device-container').html(data.results.concat);
		});
	
		$('#connection-device').trigger('change');
	}
});"
);
?>