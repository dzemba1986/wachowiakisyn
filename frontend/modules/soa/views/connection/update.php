<?php

use kartik\date\DatePicker;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var common\models\soa\Connection $connection
 * @var boolean $disableDevicesList
 * @var array $jsonType
 * @var string $portListUrl
 * @var string $deviceListUrl
 */

$form = ActiveForm::begin([
	'id'=>$connection->formName(),
    'options' => ['style' => 'padding-left:10px;padding-right:10px']
]); ?>
        
    <div class="row no-gutter">
    
    	<?= $form->field($connection, 'pay_date', [
    		'options' => ['class' => 'col-sm-3'],
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
        ]); ?>
            
        <?= $form->field($connection, 'phone', [
    		'options' => ['class' => 'col-sm-3'],
    	]) ?>
        
        <?= $form->field($connection, 'phone2', [
        	'options' => ['class' => 'col-sm-3'],
        ]) ?>
    	    
        <?php if($connection->type_id == 1 || $connection->type_id == 3) :?>
    		<?= $form->field($connection, 'mac', [
    			'options' => ['class' => 'col-sm-3'],
    		]) ?>
    	<?php endif; ?>
    	
    </div>
    
    <div class="row no-gutter">
        <?= $form->field($connection, 'device_id', [
            'options' => ['class' => 'col-sm-7'],
    	])->widget(Select2::classname(), [
    		'language' => 'pl',
           	'options' => [
           	    'placeholder' => 'Urządzenie nadrzędne',
           		'onchange' => new JsExpression("
    				$.get('" . Url::to([$portListUrl]) . "&deviceId=' + $(this).val(), function(data){
    					$('select#connection-port').html(data);
    				});
    			")
            ],
        	'pluginOptions' => [
        	    'disabled' => $disableDevicesList,
        		'allowClear' => true,
        		'minimumInputLength' => 1,
        		'language' => [
        			'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
        		],
        	    'ajax' => [
        	        'url' => Url::to([$deviceListUrl]),
        	        'dataType' => 'json',
        	        'data' => new JsExpression("function(params) {
    					return {
    						q : params.term,
                            type : " . $jsonType . "
    					};
    				}")
        	    ],
    	    	'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    	    	'templateResult' => new JsExpression('function(device) { return device.concat; }'),
    	    	'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
        	]
    	]) ?>
        
    	<?= $form->field($connection, 'port', [
    	    'options' => ['class' => 'col-sm-2'],
    	])->dropDownList([], ['prompt'=>'port', 'disabled' => $disableDevicesList]) ?>
    	
    	<?= $form->field($connection, 'close_date', [
    		'options' => ['class' => 'col-sm-3'],
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
		
    <div class="row no-gutter">			
    		<?= $form->field($connection, 'info', [
    			'options' => ['class' => 'col-sm-6'],
    		])->textarea(['rows' => "7", 'style' => 'resize: vertical']) ?>
    		
    		<?= $form->field($connection, 'info_boa', [
            	'options' => ['class' => 'col-sm-6'],
            ])->textarea(['rows' => "7", 'style' => 'resize: vertical']) ?>
    </div>
    
    <div class="row no-gutter">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end() ?>

<?php 
$deviceId = json_encode($connection->device_id);
$deviceListUrl = Url::to([$deviceListUrl, 'id' => $connection->device_id]);
$port = json_encode($connection->port);
$portListUrl = Url::to([$portListUrl, 'deviceId' => $connection->device_id, 'selected' => $connection->port, 'install' => true]);

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