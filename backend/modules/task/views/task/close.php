<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
/**
 * @var yii\web\View $this
 * @var app\models\Modyfication $modelTask
 * @var yii\widgets\ActiveForm $form
 */
?>
	
<?php $form = ActiveForm::begin([
	'id' => $modelTask->formName()
]); ?>

	<div class="row">

    <?= $form->field($modelTask, 'installer', [
    		'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
    ])->dropDownList(User::getIstallers(), ['multiple' => true]) ?>
   
    <?= $form->field($modelTask, 'cost', [
    		//'template' => "{input}\n{hint}\n{error}",
    		'options' => ['placeholder' => 'Koszt', 'class' => 'col-md-6', 'style' => 'padding-left: 5px;']
    ])->label('Koszt i status') ?>
    
    <?= $form->field($modelTask, 'status', [
    		'template' => "{input}\n{hint}\n{error}",
    		'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
    ])->dropDownList([1 => 'Wykonane', 0 => 'Niewykonane'], ['prompt' => 'Status']) ?>
    
    </div>
    
    <?php if (is_object($modelConnection)) : ?>
    
   	<?= $form->field($modelConnection, 'mac') ?>
   	
   	
   	<?= Html::label('Komentarz') ?>
   	<?= Html::textarea('desc', '', ['class' => 'form-control', 'maxlength' => 1000, 'style' => 'resize: vertical']) ?>
    <div class="help-block"></div>
    
    <?php else : ?>
    
    <?= $form->field($modelTask, 'description')->textarea(['maxlength' => 1000, 'style' => 'resize: vertical']) ?>
    
    <?php endif; ?>
         
    <div class="form-group">
        <?= Html::submitButton('Zamknij', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>



<script>

$('#<?= $modelTask->formName()?>').on('beforeSubmit', function(e){

 	$.post(
  		$(this).attr("action"), // serialize Yii2 form
  		$(this).serialize()
 	).done(function(result){
		
// 		console.log(result);
 		if(result == 1){
 			$($(this)).trigger('reset');
			$('#modal-close-task').modal('hide');
 			$.pjax.reload({container:'#task-grid-pjax'});
 		}
 		else{
		
 			//$('#message').html(result);
            alert(result);
 		}
 	}).fail(function(){
 		console.log('server error');
 	});
	return false;				
});

</script>