<?php

use backend\models\Manufacturer;
use backend\models\Model;
use backend\models\OpticalSplitter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\OpticalSplitter $device
 */ 
?>

<div class="add-store-form">

    <?php $form = ActiveForm::begin([
    	'id' => $device->formName(),
    	'validationUrl' => Url::to(['validation'])	
    		
    ]); ?>    
    
    <?= $form->field($device, 'manufacturer_id')->dropDownList(
        ArrayHelper::map(Manufacturer::find()->orderBy('name')->all(), 'id', 'name'),
        [
            'prompt' => 'Wybierz producenta', 
            'onchange' => new JsExpression("
                $('.field-opticalsplitter-model_id').removeAttr('style');
                $('.field-opticalsplitter-serial').removeAttr('style');
                $('.field-opticalsplitter-mac').removeAttr('style');
                $('.field-opticalsplitter-desc').removeAttr('style');
                $.get('" . Url::to(['model/list']) . "&typeId=" . OpticalSplitter::TYPE . "&manufacturerId=' + $(this).val(), function(data) {
                    $('select#opticalsplitter-model_id').html(data);
                });
            ")
        ]
    )?>
    
    <?= $form->field($device, 'model_id', ['options' => ['style' => ['display' => 'none']]])->dropDownList(
        ArrayHelper::map(Model::find()->orderBy('name')->all(), 'id', 'name'),
        ['prompt' => 'Wybierz model']
    )?>
    
    <?= $form->field($device, 'serial', 
        [
            'enableAjaxValidation' => true, 
            'validateOnChange' => false,
            'options' => ['style' => ['display' => 'none']],
        ]  
    )?>
    
    <?= $form->field($device, 'desc', ['options' => ['style' => ['display' => 'none']]])->textarea()?>
    
    <div class="form-group">
        <?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>
    </div>
       
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$(function() {
    $('.modal-header h4').html('Dodaj radiolinię');

	$('#{$device->formName()}').on('beforeSubmit', function(e){
	
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
	 			$(form).trigger('reset');
				$('#modal-sm').modal('hide');
	 			$.pjax.reload({container: '#store-grid-pjax'});
	 		}
	 		else{
	 			$.growl.error({ message: 'Błąd dodawania splittera optycznego'});
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