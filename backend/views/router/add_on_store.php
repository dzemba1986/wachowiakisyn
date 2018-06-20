<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Manufacturer;
use backend\models\Model;
use yii\helpers\Url;
use backend\models\Swith;
use backend\models\Router;

/* @var $this yii\web\View */
/* @var $model backend\models\Installation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="add-store-form">

    <?php $form = ActiveForm::begin([
    	'id' => $device->formName(),
    	'validationUrl' => Url::toRoute(['validation', 'type' => Swith::TYPE])	
    		
    ]); ?>    
    
    <?= $form->field($device, 'manufacturer')->dropDownList(
        ArrayHelper::map(Manufacturer::find()->orderBy('name')->all(), 'id', 'name'),
        [
            'prompt' => 'Wybierz producenta', 
            'onchange' => '$(".field-router-model").removeAttr("style");'
                . '$(".field-router-serial").removeAttr("style");'
                . '$(".field-router-mac").removeAttr("style");'
                . '$(".field-router-desc").removeAttr("style");'
                . '$.get( "' . Url::toRoute('model/list') . '&typeId=" + '. Router::TYPE .' + "&manufacturerId=" + $(this).val(), function(data) {'
                    . '$("select#router-model").html(data);'   
                . '} )'            
        ]
    )?>
    
    <?= $form->field($device, 'model', ['options' => ['style' => ['display' => 'none']]])->dropDownList(
        ArrayHelper::map(Model::find()->orderBy('name')->all(), 'id', 'name'),
        [
            'prompt' => 'Wybierz model', 
        ]
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
    $('.modal-header h4').html('Dodaj router');

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
	 			$.growl.error({ message: 'Błąd dodawania routera'});
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