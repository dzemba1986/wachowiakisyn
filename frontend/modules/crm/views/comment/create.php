<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var Comment $comment
 * @var ActiveForm $form
 */

?>

<?php $form = ActiveForm::begin(['id' => $comment->formName()]); ?>

	<?= $form->field($comment, 'description')->textarea(['rows' => '4', 'style' => 'resize: vertical', 'maxlength' => 1000, 'placeholder' => 'Dodaj komentarz']) ?>
	
	<div class="form-group">
        <?= Html::submitButton('Dodaj', ['class' => 'btn btn-success']) ?>
    </div>
	
			

<?php ActiveForm::end(); ?>


<?php
$js = <<<JS
$(function(){
    $('.modal-header h4').html('Dodaj komentarz');

	$('#{$comment->formName()}').on('beforeSubmit', function(e){

	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result){
			
	 		if(result == 1){
	 			$(this).trigger('reset');
				$('#modal').modal('hide');
	 			$.pjax.reload({container:'#task-grid-pjax'});
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