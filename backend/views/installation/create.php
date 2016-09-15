<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\models\Device;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model backend\models\Installation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="create-installation-form">

    <?php $form = ActiveForm::begin(['id'=>$modelInstallation->formName()]); ?>
    
    <div class="row">
    
    <?php $label = $modelConnection->type == 1 ? 'przełącznik' : 'bramkę'; ?>
    
    <?= $form->field($modelConnection, 'device', [
    		'options' => ['class' => 'col-md-9', 'style' => 'padding-right: 5px;'],
    	])->label("Wybierz $label")->widget(Select2::classname(), [
    		'language' => 'pl',
            'options' => [
            	'placeholder' => 'Urządzenie nadrzędne',
            	'onchange' => new JsExpression("
            		$.get('" . Url::toRoute('tree/select-list-port') . "&device=' + $('select#connection-device').val() + '&mode=free', function(data){
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
    				'url' => Url::toRoute('device/list'),
    				'dataType' => 'json',
    				'data' => new JsExpression("function(params) {
    					return {
    						q : params.term,
    						type : $modelConnection->type == 1 ? 2 : 3
						}; 
					}")
	    		],
	    		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
	    		'templateResult' => new JsExpression('function(device) { return device.concat; }'),
	    		'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
    		]
    	])     
    ?>
    
    <?= $form->field($modelConnection, 'port', [
    	'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 5px;']
    ])->dropDownList([], ['prompt'=>'port']) ?>
    
    </div>
    
    <div class="row">
    
    <?= $form->field($modelInstallation, 'wire_user', [
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
    ])->dropDownList(User::getIstallers(), ['multiple' => true]) ?>
    
    <?= $form->field($modelInstallation, 'wire_date', [
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
    ])->label('Data i długość')->widget(DatePicker::className(), [
    	'model' => $modelInstallation,
    	'attribute' => 'wire_date',
        'language'=>'pl',
    	'removeButton' => false,
        'pluginOptions' => [
        	'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
            'endDate' => '0d', //wybór daty max do dziś
        ]
	]) ?>

    <?= $form->field($modelInstallation, 'wire_length', [
    	'template' => "{input}\n{hint}\n{error}",
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
    ])->textInput(['maxlength' => true]) ?>

	</div>
	
	<?= $form->field($modelConnection, 'info')->textarea(['style' => 'resize: vertical']) ?>

    <div class="form-group">
        <?= Html::submitButton($modelInstallation->isNewRecord ? 'Dodaj' : 'Update', ['class' => $modelInstallation->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>

$(function(){

	var device = <?= json_encode($modelConnection->device); ?>

	if (device){
		$.getJSON("<?= Url::toRoute(["device/list", "id" => $modelConnection->device])?>", function(data){
			$("#select2-connection-device-container").html(data.results.concat);
		});
	
		$("#connection-device").trigger("change");
	}

	
	$(".modal-header h4").html("<?= $modelConnection->modelAddress->fullAddress ?>");

	$('#<?= $modelInstallation->formName(); ?>').on('beforeSubmit', function(e){

		var form = $(this);
	 	$.post(
	  		form.attr("action"), // serialize Yii2 form
	  		form.serialize()
	 	).done(function(result){
			
//	 		console.log(result);
	 		if(result == 1){
	 			$(form).trigger('reset');
				$('#modal-create-installation').modal('hide');
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
})
</script>