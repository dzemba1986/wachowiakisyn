<?php

use backend\models\Camera;
use backend\models\Manufacturer;
use backend\models\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** 
 * @var yii\web\View $this
 * @var backend\models\Camera $device
 */ 
?>

<div class="add-store-form">

    <?php $form = ActiveForm::begin([
    	'id' => $device->formName(),
    	'validationUrl' => Url::to(['camera/validation'])	
    		
    ]); ?>    
    
    <?= $form->field($device, 'manufacturer_id')->dropDownList(
        ArrayHelper::map(Manufacturer::find()->orderBy('name')->all(), 'id', 'name'),
        [
            'prompt' => 'Wybierz producenta', 
            'onchange' => new JsExpression("
                $('.field-camera-model_id').removeAttr('style');
                $('.field-camera-serial').removeAttr('style');
                $('.field-camera-mac').removeAttr('style');
                $('.field-camera-desc').removeAttr('style');
                $.get('" . Url::to(['model/list']) . "&typeId=" . Camera::TYPE . "&manufacturerId=' + $(this).val(), function(data) {
                    $('select#camera-model_id').html(data);
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
    
    <?= $form->field($device, 'mac', 
        [       
            'enableAjaxValidation' => true, 
            'validateOnChange' => false,
            'options' => ['style' => ['display' => 'none']],
        ]
    )?>
    
    <?= $form->field($device, 'desc', ['options' => ['style' => ['display' => 'none']]])->textarea()?>
    
    <div class="form-group">
        <?= Html::submitButton($device->isNewRecord ? 'Dodaj' : 'Update', ['class' => $device->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
       
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$(function() {
	$('#{$device->formName()}').on('beforeSubmit', function(e){
	
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
	 			$(form).trigger('reset');
				$('#modal-store').modal('hide');
	 			$.pjax.reload({container: '#store-grid-pjax'});
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