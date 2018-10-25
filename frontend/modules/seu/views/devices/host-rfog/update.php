<?php

use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\Address $address
 * @var backend\models\Host $device
 */

GrowlAsset::register($this);

$form = ActiveForm::begin([
	'id' => $device->formName(),
])?>
	
	<div class="col-md-4">
	
     	<div class="row">
     	
    		<?= $form->field($device, 'proper_name', [
    			'options' => ['class' => 'col-sm-13', 'style' => 'padding-left: 0px; padding-right: 0px;']
    		]) ?>
				
		</div>
    
    	<div class="row">
    	
    		<?= $form->field($device, 'desc', [
    			'options' => ['class' => 'col-sm-13', 'style' => 'padding-left: 0px; padding-right: 0px;']
    		])->textarea() ?>	
    
            <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
            
  		</div>
	
	</div>
	
<?php ActiveForm::end() ?>

<?php
$urlView = Url::to(['tabs-view']);

$js = <<<JS
$(function() {

    $('#{$device->formName()}').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
    		if(result == 1) {
     			$('#device_desc').load('{$urlView}&id=' + {$device->id});
                $.notify('Zaktualizowano hosta.', {
                    type : 'success',
                    placement : { from : 'top', align : 'right'},
                });
     		} else {
     			$.notify('Błąd aktualizacji urządzenia.', {
                    type : 'danger',
                    placement : { from : 'top', align : 'right'}, 
                });
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