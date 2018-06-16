<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var integer $id
 * @var backend\models\Tree $link
 */ 

$form = ActiveForm::begin([
	'id' => 'add-device-form',
    ])?>
    
    <?= $form->field($link, 'parent_port')->dropDownList(['']) ?>
    
    <?= $form->field($virtual, 'mac') ?>
    
	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>

<?php
$urlPortList = Url::to(['tree/list-port']);

$js = <<<JS
$(function(){

    $('.modal-header h4').html('Dodaj virtualkę');

    $.get('{$urlPortList}&deviceId={$id}', function(data){
    	$("select[name='Tree[parent_port]']").html(data);
    } );

	$("#add-device-form").on('beforeSubmit', function(e){
		var form = $(this);
     	$.post(
      		form.attr("action"),
      		form.serialize()
     	).done(function(result){
 			if(result == 1){
                var tree = $("#device_tree").jstree(true);

				$('#modal-sm').modal('hide');
                tree.refresh();
			}
 			else {
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