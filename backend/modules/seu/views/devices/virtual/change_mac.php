<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

/**
 * @var yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 * @var backend\models\Virtual $virtual
 */

$form = ActiveForm::begin([
	'id' => $virtual->formName(),
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['validation', 'id' => $virtual->id]),
    'validateOnType' => true
])?>
	
	<?= $form->field($virtual, 'mac') ?>

    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary save', 'data-clipboard-text' => '', 'disabled' => true]) ?>
    
    <?= Html::button('Skrypt', ['class' => 'btn change', 'onclick' => new JsExpression("
        $.get('" . Url::to(['get-change-mac-script']) . "&id=" . $virtual->id . "&newMac=' + $('#virtual-mac').val(), function(data){
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

    clipboard
        .on('success', function(e) {
            $.growl.notice({ message: 'Skrypt w schowku'});
            e.clearSelection();
        })
        .on('error', function(e) {
            $.growl.error({ message: 'Brak skryptu w schowku'});
        });

    $('.modal-header h4').html('Zmień MAC');
	
    $('#{$virtual->formName()}').on('beforeSubmit', function(e) {

    	var form = $(this);
     	$.post(
      		form.attr("action"),
      		form.serialize()
     	).done(function(result){
     		if(result == 1){
                $('#modal-sm').modal('hide');
    			$('#device_desc').load('{$urlView}&id=' + {$virtual->id});
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