<?php

use backend\models\Manufacturer;
use backend\models\Model;
use backend\models\Ups;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

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
                $('.field-ups-model_id').removeAttr('style');
                $('.field-ups-serial').removeAttr('style');
                $('.field-ups-mac').removeAttr('style');
                $('.field-ups-desc').removeAttr('style');
                $.get('" . Url::to(['model/list']) . "&typeId=" . Ups::TYPE . "&manufacturerId=' + $(this).val(), function(data) {
                    $('select#ups-model_id').html(data);
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
        <?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>
    </div>
       
    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$(function() {
    $('.modal-header h4').html('Dodaj UPS');

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
	 			$.growl.error({ message: 'Błąd dodawania UPS'});
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