<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 */

$form = ActiveForm::begin([
	'id' => 'move',
])?>
    <?= Html::label('Port rodzica') ?>
    
    <?= Html::dropDownList('newParentPort', null, [], ['class' => 'form-control']) ?>
    
    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>

<?php
$urlPortList = Url::to(['tree/list-port']);

$js = <<<JS
$(function(){
    $.get('{$urlPortList}&deviceId={$newParentId}', function(data){
    	$("select[name='newParentPort']").html(data);
    } );

    $('#move').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
                var tree = $("#device_tree").jstree(true);

				$('#modal-tree').modal('hide');
                tree.refresh();
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