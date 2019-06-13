<?php

use common\models\crm\DeviceTask;
use common\models\crm\TaskCategory;
use kartik\growl\GrowlAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\crm\DeviceTask $task
 * @var yii\widgets\ActiveForm $form
 */

GrowlAsset::register($this);

$form = ActiveForm::begin(['id'=>$task->formName()]);
	
    echo $form->field($task, 'category_id')->dropDownList(ArrayHelper::map(TaskCategory::find()->where(['parent_id' => DeviceTask::TYPE])->asArray()->all(), 'id', 'name'));
	
    echo Html::submitButton('Edytuj', ['class' => 'btn btn-primary']);

ActiveForm::end();

$js = <<<JS
$(function(){
    $( '#modal-sm-title' ).html('Edycja zgłoszenia');

	$('#{$task->formName()}').on('beforeSubmit', function(e) {
	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result) {
	 		if(result[0] == 1) {
	 			$(this).trigger('reset');
				$('#modal-sm').modal('hide');
	 			$.pjax.reload({container:'#task-grid-pjax'});
                $.notify(result[1], {
                    type : 'success',
                    placement : { from : 'top', align : 'right'},
                });
	 		} else {
				$('#modal-sm').modal('hide');
	 			$.notify(result[1], {
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