<?php

use backend\models\InstallationType;
use common\models\User;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $installation backend\models\Installation
 * @var $connection backend\models\Connection
 * @var $form yii\widgets\ActiveForm
 */
?>

<div class="create-installation-form">

    <?php $form = ActiveForm::begin(['id' => $installation->formName()]); ?>
    
    <div class="row">
    
    <?php $labelDevice = $connection->type_id == 1 || $connection->type_id == 3 ? 'przełącznik dostępowy' : 'bramkę'; ?>
    
    <?= $form->field($connection, 'device_id', [
    		'options' => ['class' => 'col-md-9', 'style' => 'padding-right: 5px;'],
    	])->label("Wybierz $labelDevice")->widget(Select2::classname(), [
    		'language' => 'pl',
            'options' => [
            	'placeholder' => 'Urządzenie nadrzędne',
            	'onchange' => new JsExpression("
            		$.get('" . Url::to(['tree/list-port']) . "&deviceId=' + $(this).val() + '&install=true', function(data){
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
    				'url' => Url::toRoute('device/list-from-tree'),
    				'dataType' => 'json',
    				'data' => new JsExpression("function(params) {
    					return {
    						q : params.term,
    						type_id : $connection->type_id == 1 || $connection->type_id == 3 ? [2] : [3],
						}; 
					}")
	    		],
	    		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
	    		'templateResult' => new JsExpression('function(device) { return device.concat; }'),
	    		'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
    		]
    	])     
    ?>
    
    <?= $form->field($connection, 'port', [
    	'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 5px;']
    ])->dropDownList([], ['prompt'=>'port']) ?>
    
    </div>
    
    <div class="row">
    
    <?= $form->field($installation, 'wire_user', [
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
    ])->dropDownList(User::getIstallers(), ['multiple' => true]) ?>
    
    <?= $form->field($installation, 'wire_date', [
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
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
    	'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 5px; padding-right: 5px;']
    ])->textInput(['maxlength' => true, 'placeholder' => $installation->getAttributeLabel('wire_length')]) ?>

	<?= $form->field($installation, 'type_id', [
	    'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 5px;'],
        'template' => "{input}\n{hint}\n{error}",
	])->dropDownList(ArrayHelper::map(InstallationType::findAll($connection->getInstallationTypeIds()), 'id', 'name')) ?>
	
	</div>
	
	<?= $form->field($connection, 'info')->textarea(['style' => 'resize: vertical']) ?>

    <div class="form-group">
        <?= Html::submitButton($installation->isNewRecord ? 'Dodaj' : 'Update', ['class' => $installation->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$deviceId = json_encode($connection->device_id);
$deviceListUrl = Url::to(['device/list-from-tree', 'id' => $connection->device_id]);
$port = json_encode($connection->port);
$portListUrl = Url::to(['tree/list-port', 'deviceId' => $connection->device_id, 'selected' => $connection->port, 'install' => true]);

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

    $(".modal-header h4").html('{$connection->address->toString()}');

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