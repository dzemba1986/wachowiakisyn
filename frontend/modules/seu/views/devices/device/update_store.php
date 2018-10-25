<?php

use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\Camera $device
 */

GrowlAsset::register($this);

$form = ActiveForm::begin([
	'id' => $device->formName(),
	'validationUrl' => Url::to(['validation', 'id' => $device->id])
])?>
    
    <?= $form->field($device, 'desc')->textarea()?>
	
	<?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
	
<?php ActiveForm::end();

$js = <<<JS
$(function(){
    $('.modal-header h4').html('Edycja');

	$('#{$device->formName()}').on('beforeSubmit', function(e) {
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
	 			$(form).trigger('reset');
    			$('#modal-sm').modal('hide');
     			$.pjax.reload({container: '#store-grid-pjax'});
                $.notify('Zaktualizowano urządzenie.', {
                    type: 'success',
                    placement : { from : 'top', align : 'right'},
                });
	 		}
	 		else{
	 			$.notify('Błąd aktualizacji urządzenia.', {
                    type: 'danger',
                    placement : { from : 'top', align : 'right'}, 
                });
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