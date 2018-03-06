<?php
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\CameraTaskForm 
 */
?>
<div class="task-create">
    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => $model->formName()]); ?>
                <?= $form->field($model, 'device_id', [
    				])->label('Kamera')->widget(Select2::classname(), [
    					'language' => 'pl',
			    		'pluginOptions' => [
			    			'allowClear' => true,
			    			'minimumInputLength' => 1,
			    			'language' => [
			    				'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
			    			],
			    			'ajax' => [
		    					'url' => Url::to(['device/list-camera']),
			    				'dataType' => 'json',
			    				'data' => new JsExpression("function(params) {
			    					return {
			    						q : params.term
									}; 
								}")
				    		],
		    				'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		    				'templateResult' => new JsExpression('function(results) { return results.alias; }'),
		    				'templateSelection' => new JsExpression('function (results) { return results.alias; }'),
			    		]
    				])     
    			?>
                <?= $form->field($model, 'description')->textarea(['rows' => '4', 'style' => 'resize: vertical', 'maxlength' => 1000, 'placeholder' => 'Opisz krótko usterkę']) ?>
                <div class="form-group">
                    <?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$(function(){
	$('#{$model->formName()}').on('beforeSubmit', function(e){

	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result){
			
	 		if(result == 1){
	 			$(this).trigger('reset');
				$('#modal-task').modal('hide');
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