<?php
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var backend\models\forms\AddHostForm $model
 * @var \yii\widgets\ActiveForm $form
 */

$form = ActiveForm::begin([
	'id' => $model->formName(),
    'validationUrl' => Url::to(['add-host-validation'])
]);
?>
    <div class="row">
    	<?= $form->field($model, 'deviceId', [
    	    'options' => ['class' => 'col-sm-9', 'style' => 'padding-right: 0px;'],
        	])->widget(Select2::classname(), [
        		'language' => 'pl',
               	'options' => [
               	    'placeholder' => 'Urządzenie nadrzędne',
               		'onchange' => new JsExpression("
    					$.get('" . Url::to(['link/list-port']) . "&deviceId=' + $(this).val(), function(data) {
    						$('select[name=\"AddHostRfogForm[port]\"]').html(data);
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
    	    		    'url' => Url::to(['device/list-from-tree']),
    	    			'dataType' => 'json',
    	    			'data' => new JsExpression("function(params) { return {
    	    				q : params.term,
                            type : " . $jsonType . "
    					};}")
    		    	],
    		    	'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    		    	'templateResult' => new JsExpression('function(device) { return device.concat; }'),
    		    	'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
    	    	]
        	]) ?>
        
    	<?= $form->field($model, 'port', [
    	    'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 3px;'],
    	])->dropDownList([]) ?>
    </div>
    	
    <?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>	

<?php
$deviceId = json_encode($model->deviceId);
$port = json_encode($model->port);
$deviceListUrl = Url::to(['optical-splitter/list-from-tree', 'id' => $model->deviceId]);
$portListUrl = Url::to(['link/list-port', 'deviceId' => $model->deviceId, 'selected' => $model->port]);

$js = <<<JS
$(function() {
    var deviceId = {$deviceId}; //jeżeli urządzenie jest ustawione pobiera jego wartość id
    var port = {$port}; //jeżeli port jest ustawiony pobiera jego wartość

    $('.modal-header h4').html('{$model->address}');

    if (deviceId) {
		$.getJSON('{$deviceListUrl}', function(data) {
			$('#select2-addhostrfogform-deviceid-container').html(data.results.concat);
		});

        if (port !== null) {
            $.get('{$portListUrl}', function(data) {
                $('select[name="AddHostRfogForm[port]"]').html(data);
            });
        }
	}

    $('#{$model->formName()}').on('beforeSubmit', function(e) {
		var form = $(this);
     	$.post(
      		form.attr('action'),
      		form.serialize()
     	).done(function(result){
 			if(result){
                //window.location.replace(result);
 			}
 			else{
 				alert(result);
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