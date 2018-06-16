<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var backend\models\Connection $connection 
 */
?>

<div class="connection-update">

    <?php $form = ActiveForm::begin([
		'id'=>$connection->formName(),
    ])?>
        
    <div class="row">
    
    	<?= $form->field($connection, 'pay_date', [
			'options' => ['class' => 'col-sm-3', 'style' => 'padding-right: 3px;'],
		])->widget(DatePicker::className(), [
        	'model' => $connection,
            'attribute' => 'pay_date',
			'pickerButton' => false,
			'disabled' => $connection->socket > 0 ? false : true,
			'language' => 'pl',
            'pluginOptions' => [
            	'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'endDate' => '0d',
            ]
        ])?>
            
        <?= $form->field($connection, 'phone', [
    		'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 0px; padding-right: 3px;'],
    	]) ?>
	    
	    <?= $form->field($connection, 'phone2', [
	    	'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 0px; padding-right: 3px;'],
	    ]) ?>
		    
	    <?php if($connection->type_id == 1 || $connection->type_id == 3) :?>
			<?= $form->field($connection, 'mac', [
				'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 0px;'],
			]) ?>
		<?php endif; ?>
		
    </div>
    
    <?php if (($allConnections == 0 && empty($connection->host_id)) || $connection->type_id == 2) : ?>
    <div class="row">
        
        <?= $form->field($connection, 'device_id', [
			'options' => ['class' => 'col-sm-7', 'style' => 'padding-right: 3px;'],
    	])->widget(Select2::classname(), [
    		'language' => 'pl',
           	'options' => [
           	    'placeholder' => 'Urządzenie nadrzędne',
           		'onchange' => new JsExpression("
					$.get('" . Url::to(['tree/list-port']) . "&deviceId=' + $(this).val(), function(data){
						$('select#connection-port').html(data);
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
	    		    'url' => $connection->type_id == 2 ? Url::to(['gateway-voip/list-from-tree']) : Url::to(['swith/list-from-tree']),
	    			'dataType' => 'json',
	    			'data' => new JsExpression("function(params) { return {
	    				q : params.term,
					};}")
		    	],
		    	'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		    	'templateResult' => new JsExpression('function(device) { return device.concat; }'),
		    	'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
	    	]
    	]) ?>
    
		<?= $form->field($connection, 'port', [
			'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 0px; padding-right: 3px;'],
		])->dropDownList([], ['prompt'=>'port']) ?>
		
		<?= $form->field($connection, 'close_date', [
			'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 0px;'],
		    'labelOptions' => ['style' => 'color:red']
		])->widget(DatePicker::className(), [
        	'model' => $connection,
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
	<?php elseif ($allConnections > 0 || !empty($connection->host_id)) : ?>
		<h4>Host już istneje</h4>
	<?php endif; ?>
	
	<div class="row">			
			<?= $form->field($connection, 'info', [
				'options' => ['class' => 'col-sm-6', 'style' => 'padding-right: 3px;'],
			])->textarea(['rows' => "7", 'style' => 'resize: vertical']) ?>
			
			<?= $form->field($connection, 'info_boa', [
	        	'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 0px;'],
	        ])->textarea(['rows' => "7", 'style' => 'resize: vertical']) ?>
    </div>
    
    <?= Html::submitButton($connection->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
    
    <?php ActiveForm::end() ?>
</div>

<?php 
$deviceId = json_encode($connection->device_id);
$deviceListUrl = Url::to(['device/list-from-tree', 'id' => $connection->device_id]);
$port = json_encode($connection->port);
$portListUrl = Url::to(['tree/list-port', 'deviceId' => $connection->device_id, 'selected' => $connection->port, 'install' => true]);

$js = <<<JS
$(function(){
    var deviceId = {$deviceId}; //jeżeli urządzenie jest ustawione pobiera jego wartość id
    var port = {$port}; //jeżeli port jest ustawiony pobiera jego wartość

    $('.modal-header h4').html('{$connection->address->toString()}');

    if (deviceId) {
		$.getJSON('{$deviceListUrl}', function(data){
			$('#select2-connection-device_id-container').html(data.results.concat);
		});
        
        if (port !== null) {
            $.get('{$portListUrl}', function(data){
                $('select#connection-port').html(data);
            });
        }
	}

	$('#{$connection->formName()}').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'), // serialize Yii2 form
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
				$('#modal').modal('hide');
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
});
JS;

$this->registerJs($js);
?>