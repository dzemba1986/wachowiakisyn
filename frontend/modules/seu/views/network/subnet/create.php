<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $subnet backend\models\Subnet
 * @var $form yii\widgets\ActiveForm
 */

$form = ActiveForm::begin([
	'id' => $subnet->formName()
]); ?>

    <?= $form->field($subnet, 'ip'); ?>
    
    <?= $form->field($subnet, 'desc'); ?>

    <?= $form->field($subnet, 'dhcp')->checkbox(); ?>
    
	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>
	
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS

$(function() {

    $('.modal-header h4').html('Dodaj podsieć');

    $('#{$subnet->formName()}').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
    		
     		if(result == 1){
     			$(form).trigger('reset');
                $('#modal-update-net').modal('hide');
 			    $.pjax.reload({container: '#subnet-grid-pjax'});
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