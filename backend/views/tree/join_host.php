<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 */

$form = ActiveForm::begin([
    'id' => 'join-host',
]);?>

<div class="form-group">
    	
	<?= Html::label('Wybierz hosta do którego chcesz przypisać umowę :') ?>
    				
	<?= Html::radioList('host', null, ArrayHelper::map($hosts, 'id', 'concat')) ?>
	
</div>

<?= Html::submitButton('Dołącz', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>

<?php
$urlReplace = Url::toRoute(['tree/index', 'id' => $hosts[0]['id'] . '.0'], true);

$js = <<<JS
$(function() {
    $('#join-host').on('beforeSubmit', function(e){
		var form = $(this);
     	$.post(
      		form.attr('action'),
      		form.serialize()
     	).done(function(result){
 			if(result == 1){
                window.location.replace('{$urlReplace}');
 			}
 			else{
 				alert(result);
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