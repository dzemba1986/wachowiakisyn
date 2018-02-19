<?php
use backend\models\Vlan;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
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
    'validationUrl' => Url::to(['validator/add-host'])
]);
?>

<div class="row">
	<?= $form->field($model, 'deviceId', [
	    'options' => ['class' => 'col-sm-7', 'style' => 'padding-right: 0px;'],
    	])->widget(Select2::classname(), [
    		'language' => 'pl',
           	'options' => [
           	    'placeholder' => 'Urządzenie nadrzędne',
           		'onchange' => new JsExpression("
					$.get('" . Url::to(['tree/list-port']) . "&deviceId=' + $(this).val(), function(data){
						$('select[name=\"AddHostForm[port]\"]').html(data);
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
	    			'data' => new JsExpression("function(params) { return {
	    				q : params.term,
						type_id : $model->typeId == 1 || $model->typeId == 3 ? [2] : [3],
					};}")
		    	],
		    	'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		    	'templateResult' => new JsExpression('function(device) { return device.concat; }'),
		    	'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
	    	]
    	]) ?>
    	
	<?= $form->field($model, 'port', [
	    'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 3px; padding-right: 3px;'],
	])->dropDownList([]) ?>
	
	<?= $form->field($model, 'mac', [
	    'enableAjaxValidation' => true,
	    'options' => ['class' => 'col-sm-3', 'style' => 'padding-left: 0px;'],
	]) ?>
</div>
	
<div class="row">
	
</div>

<div class="row">
        <?= $form->field($model, 'vlanId', [
            'options' => ['class' => 'col-sm-3', 'style' => 'padding-right: 3px;'],
        ])->dropDownList(ArrayHelper::map(Vlan::find()->select('id')->orderBy('id')->all(), 'id', 'id'), [
        	'prompt' => 'Vlan',
        	'onchange' => new JsExpression("
        		$.get('" . Url::toRoute('subnet/list') . "&vlanId=' + $(this).val(), function(data){
            		$('select[name=\"AddHostForm[subnetId]\"]').html(data).trigger('change');
        		});	
        	")  	
        ]) ?>
        
        <?= $form->field($model, 'subnetId', [
            'options' => ['class' => 'col-sm-5', 'style' => 'padding-left: 0px; padding-right: 3px;'],
        ])->dropDownList([], [
        	'prompt' => 'Podsieć',
        	'onchange' => new JsExpression("
        		$.get('" . Url::toRoute('ip/select-list') . "&subnet=' + $(this).val() + '&mode=free', function(data){
    				$('select[name=\"AddHostForm[ip]\"]').html(data);
    			});
        	")  	
        ]) ?>
        
        <?= $form->field($model, 'ip', [
            'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px;'],
        ])->dropDownList([], [
        	'prompt' => 'Ip',  	
        ]) ?>
</div>

<?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>	

<?php
$deviceId = json_encode($model->deviceId);
$port = json_encode($model->port);
$deviceListUrl = Url::to(['device/list-from-tree', 'id' => $model->deviceId]);
$portListUrl = Url::to(['tree/list-port', 'deviceId' => $model->deviceId, 'selected' => $model->port]);

$js = <<<JS
$(function() {
    var deviceId = {$deviceId}; //jeżeli urządzenie jest ustawione pobiera jego wartość id
    var port = {$port}; //jeżeli port jest ustawiony pobiera jego wartość

    $('.modal-header h4').html('{$model->address}');

    if (deviceId) {
		$.getJSON('{$deviceListUrl}', function(data){
			$('#select2-addhostform-deviceid-container').html(data.results.concat);
		});
	
        if (port) {
            $.get('{$portListUrl}', function(data){
                $('select[name="AddHostForm[port]"]').html(data);
            });
        }
	}

    $('#{$model->formName()}').on('beforeSubmit', function(e){
		var form = $(this);
     	$.post(
      		form.attr('action'),
      		form.serialize()
     	).done(function(result){
 			if(result){
                window.location.replace(result);
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