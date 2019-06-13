<?php

use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var Comment $comment
 * @var ActiveForm $form
 */

GrowlAsset::register($this);

$form = ActiveForm::begin(['id' => $comment->formName()]);

    echo $form->field($comment, 'desc')->textarea(['rows' => '4', 'style' => 'resize: vertical', 'maxlength' => 1000, 'placeholder' => 'Dodaj komentarz']);
	
	echo Html::submitButton('Dodaj', ['class' => 'btn btn-success']);
	
ActiveForm::end();

$js = <<<JS
$(function() {
    $('#modal-title').html('Dodaj komentarz');

	$('#{$comment->formName()}').on('beforeSubmit', function(e){

	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result) {
	 		if(result[0] == 1){
	 			$(this).trigger('reset');
				$('#modal').modal('hide');
	 			$.pjax.reload({container:'#task-grid-pjax'});
                $.notify(result[1], {
                    type : 'success',
                    placement : { from : 'top', align : 'right'},
                });
	 		} else {
                $.notify(result[1], {
                    type : 'danger',
                    placement : { from : 'top', align : 'right'},
                });
	 		}
	 	}).fail(function() {
	 		console.log('server error');
	 	});
		return false;				
	});
});
JS;

$this->registerJs($js);
?>