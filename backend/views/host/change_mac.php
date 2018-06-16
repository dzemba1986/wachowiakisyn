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
	'id' => $host->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['validation', 'id' => $host->id]),
    'validateOnType' => true
])?>
	
	<?= $form->field($host, 'mac') ?>

    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary save', 'data-clipboard-text' => '', 'disabled' => true]) ?>
    
    <?= Html::button('Skrypt', ['class' => 'btn change', 'onclick' => new JsExpression("
        $.get('" . Url::to(['get-change-mac-script']) . "&id=" . $host->id . "&newMac=' + $('#host-mac').val(), function(data){
            $('.save').attr('disabled', false);
			$('.save').attr('data-clipboard-text', data);
		});    
    ")]) ?>

<?php ActiveForm::end() ?>

<?php 
$urlView = Url::to(['tabs-view']);

$js = <<<JS
$(function(){

    var clipboard = new Clipboard('.save');

    $('.modal-header h4').html('Zmień MAC');
	
    $('#{$host->formName()}').on('beforeSubmit', function(e) {

    	var form = $(this);
     	$.post(
      		form.attr("action"),
      		form.serialize()
     	).done(function(result){
     		if(result == 1){
                $('#modal-sm').modal('hide');
    			$('#device_desc').load('{$urlView}&id=' + {$host->id});
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