<?php
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * @var $this yii\web\View
 * @var $form yii\bootstrap\ActiveForm
 * @var $model \frontend\models\CameraTaskForm 
 */

$form = ActiveForm::begin([
    'id' => $model->formName()
]);

    echo Html::beginTag('div', ['id' => 'info', 'class' => 'alert alert-danger alert-dismissable', 'role' => 'alert', 'style' => 'display:none']);
	echo Html::button('&times;', ['class' => 'close', 'data-dismiss' => 'alert', 'aria-hidden' => 'true']);
	echo 'Zgłoszenie dla tej kamery już istnieje.';
	echo Html::endTag('div');

    echo $form->field($model, 'device_id', [
		])->label('Kamera')->widget(Select2::class, [
			'language' => 'pl',
    		'pluginOptions' => [
        		'allowClear' => true,
        		'minimumInputLength' => 1,
        		'language' => [
        			'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
        		],
        	    'ajax' => [
        	        'url' => Url::to(['/seu/devices/camera/list']),
        	        'dataType' => 'json',
        	        'data' => new JsExpression("function(params) {
        				return {
        					q : params.term,
        				};
                    }")
        	    ],
        		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        		'templateResult' => new JsExpression('function(results) { return results.alias; }'),
        		'templateSelection' => new JsExpression('function (results) { return results.alias; }'),
    		]
		]);
		
	echo $form->field($model, 'desc')->textarea(['rows' => '4', 'style' => 'resize: vertical', 'maxlength' => 1000, 'placeholder' => 'Opisz krótko usterkę']);
    echo Html::submitButton('Dodaj', ['class' => 'btn btn-primary', 'name' => 'signup-button']);

ActiveForm::end();

$js = <<<JS
$(function() {
    $('#modal-title').html('Dodaj usterkę');

    $('#cameratask-device_id').on("change", function (e) { 
        $.get('check-double', { deviceId: $(this).val()}, function(data) {
            if (data > 0) {
                $('#info').fadeTo(5000, 500).slideUp(500, function() {
                    $('#info').slideUp(500);
                }); 
            }
        }); 
    });    

	$('#{$model->formName()}').on('beforeSubmit', function(e) {
	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result) {
	 		if(result[0] == 1) {
	 			$(this).trigger('reset');
				$('#modal').modal('hide');
	 			$.pjax.reload({container:'#task-grid-pjax'});
	 		} else {
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