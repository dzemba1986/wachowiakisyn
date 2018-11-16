<?php

/**
 * @var yii\web\View $this
 * @var common\models\seu\network\Vlan $vlan
 * @var yii\widgets\ActiveForm $form
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
	'id' => $vlan->formName(),
])?>
    
    <?= $form->field($vlan, 'desc') ?>
    
	<?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
	
<?php ActiveForm::end() ?>

<?php
// $urlSubnet = Url::to(['subnet/index', 'vlan' => $subnet->vlan_id]);

$js = <<<JS
$(function() {

    $('.modal-header h4').html('Dodaj podsieć');

    $('#{$vlan->formName()}').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"),
      		form.serialize()
     	).done(function(result){
    		
     		if(result == 1){
     			$(form).trigger('reset');
                $('#modal-sm').modal('hide');
                $.pjax.reload({container: '#vlan-grid-pjax'});
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