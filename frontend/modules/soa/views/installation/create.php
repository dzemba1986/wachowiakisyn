<?php

use common\models\User;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var common\models\soa\Installation $installation 
 * @var common\models\soa\Connection $connection
 * @var array $jsonType
 * @var string $portListUrl
 * @var string $deviceListUrl 
 * @var boolean $disableDevicesList
 */

$form = ActiveForm::begin([
    'id' => $installation->formName(),
    'options' => ['style' => 'padding-left:10px;padding-right:10px']
]); ?>
    
    <div class="row no-gutter">
    
        <?= $form->field($connection, 'device_id', [
            'options' => ['class' => 'col-md-9'],
    	])->label($connection->selectDeviceLabel)->widget(Select2::classname(), [
    		'language' => 'pl',
            'options' => [
                'disabled' => $disableDevicesList,
            	'placeholder' => 'Urządzenie nadrzędne',
            	'onchange' => new JsExpression("
            		$.get('" . Url::to([$portListUrl]) . "&deviceId=' + $(this).val() + '&install=true', function(data){
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
    	]); ?>
    
    <?= $form->field($connection, 'port', [
    	'options' => ['class' => 'col-md-3']
    ])->dropDownList([], ['prompt'=>'port', 'disabled' => $disableDevicesList]) ?>
    
    </div>
    
    <div class="row no-gutter">
    
        <?= $form->field($installation, 'wire_user', [
        	'options' => ['class' => 'col-md-6']
        ])->dropDownList(User::getIstallers(), ['multiple' => true]) ?>
        
        <?= $form->field($installation, 'wire_date', [
        	'options' => ['class' => 'col-md-6']
        ])->label('Data, długość i typ')->widget(DatePicker::className(), [
        	'model' => $installation,
        	'attribute' => 'wire_date',
            'language'=>'pl',
        	'pickerButton' => false,
            'pluginOptions' => [
            	'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'endDate' => '0d',
            ]
    	]) ?>
    
        <?= $form->field($installation, 'wire_length', [
        	'template' => "{input}\n{hint}\n{error}",
        	'options' => ['class' => 'col-md-3']
        ])->textInput(['maxlength' => true, 'placeholder' => $installation->getAttributeLabel('wire_length')]) ?>
    
    	<?= $form->field($installation, 'type_id', [
    	    'options' => ['class' => 'col-md-3'],
            'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList(ArrayHelper::map($connection->installationTypeByName, 'id', 'name')) ?>
	
	</div>
	
	<?= $form->field($connection, 'info')->textarea(['style' => 'resize: vertical']) ?>

    <?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end(); ?>

<?php
$deviceId = json_encode($connection->device_id);
$deviceListUrl = Url::to([$deviceListUrl, 'id' => $connection->device_id]);
$port = json_encode($connection->port);
$portListUrl = Url::to([$portListUrl, 'deviceId' => $connection->device_id, 'selected' => $connection->port, 'install' => true]);

$js = <<<JS
$(function() {
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

	$('#{$installation->formName()}').on('beforeSubmit', function(e){

		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
	 			$(form).trigger('reset');	//TODO przy resecie wysyła ponowne zapytanie o ilość wolnych portów dla deviceId = null
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