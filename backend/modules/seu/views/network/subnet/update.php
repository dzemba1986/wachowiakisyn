<?php

/**
 * @var yii\web\View $this
 * @var common\models\seu\network\Subnet $subnet
 * @var yii\widgets\ActiveForm $form
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => $subnet->formName()
]); ?>

    <?= $form->field($subnet, 'desc'); ?>

    <?= $form->field($subnet, 'dhcp')->checkbox(); ?>
    
	<?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
	
<?php ActiveForm::end(); ?>

<?php
$urlSubnet = Url::to(['subnet/index', 'vlan' => $subnet->vlan_id]);

$js = <<<JS
$(function() {

    $('.modal-header h4').html('Edycja');

    $('#{$subnet->formName()}').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"),
      		form.serialize()
     	).done(function(result){
     		if(result == 1){
     			$(form).trigger('reset');
                $('#modal-sm').modal('hide');
                $('#subnet-grid').load('{$urlSubnet}');
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