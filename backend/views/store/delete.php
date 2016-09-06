<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use yii\helpers\Url;

?>
<div class="device-update">

<?php $form = ActiveForm::begin([
	'id' => 'delete-device-form'
    ])?>
    <center>
    <?= Html::submitButton('Tak', ['class' => 'btn btn-primary']) ?>
    </center>
<?php ActiveForm::end() ?>

</div>

<script>

$('#delete-device-form').on('beforeSubmit', function(e){

	var form = $(this);
 	$.ajax({
  		url : form.attr("action"),
  		type : 'post',
  		data : { yes: 1 }
 	}).done(function(result){
		
// 		console.log(result);
 		if(result == 1){
			$('#modal_delete_store').modal('hide');
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

</script>