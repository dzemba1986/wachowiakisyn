<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 * @var backend\models\Host $device
 */

$form = ActiveForm::begin([
	'id' => $device->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['host/validation', 'id' => $device->id]),
    'validateOnType' => true
])?>
	
	<?= $form->field($device, 'mac') ?>

    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary save', 'data-clipboard-text' => '', 'disabled' => true]) ?>
    
    <?= Html::button('Skrypt', ['class' => 'btn change', 'onclick' => new JsExpression("
        $.get('" . Url::to(['device/get-change-mac-script']) . "&deviceId=" . $device->id . "&newMac=' + $('#host-mac').val(), function(data){
            $('.save').attr('disabled', false);
			$('.save').attr('data-clipboard-text', data);
		});    
    ")]) ?>

<?php ActiveForm::end() ?>

<?php 
$urlView = Url::to(['device/tabs-view']);

$js = <<<JS
$(function(){

    var clipboard = new Clipboard('.save');
	
	clipboard.on('success', function(e) {
		console.log('Skopiowano');
	}).on('error', function(e) {
        console.log('NIE SKOPIOWANO');
    });

    $('#{$device->formName()}').on('beforeSubmit', function(e) {

    	var form = $(this);
     	$.post(
      		form.attr("action"),
      		form.serialize()
     	).done(function(result){
     		if(result == 1){
                $('#modal-change-mac').modal('hide');
    			$('#device_desc').load('{$urlView}&id=' + {$device->id});
     		}
     		else{
     			console.log(result);
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