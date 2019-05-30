<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View 
 * @var $model backend\models\Address
 * @var $form yii\widgets\ActiveForm
 */ 

$form = ActiveForm::begin([
	'id' => $model->formName(),
    'options' => ['style' => 'padding-left:10px;padding-right:10px'],
]
); 

    echo $form->field($model, 't_ulica')->widget(Select2::className(), [
		'language' => 'pl',
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => 1,
			'language' => [
				'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
            ],
			'ajax' => [
				'url' => Url::to(['list']),
				'dataType' => 'json',
				'data' => new JsExpression("function(params) {
					return {
						q : params.term
					};
				}")
			],
			'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
			'templateResult' => new JsExpression('function(ulic) { return ulic.concat; }'),
			'templateSelection' => new JsExpression('function (ulic) { 

				//podczas wyboru odpowiedniej ulicy ustawia pozostałe wartości: t_gmi, t_miasto......
				$("input#addressshort-t_gmi").val(ulic.gmi); 
				$("input#addressshort-t_miasto").val(ulic.sym);
				$("input#addressshort-ulica_prefix").val(ulic.cecha);
				
				if (ulic.nazwa_2 == null){
					$("input#addressshort-ulica").val(ulic.nazwa_1);
				} else {	
					$("input#addressshort-ulica").val(ulic.nazwa_2 + ulic.nazwa_1);
				}

				return ulic.concat; 
			}')
		]
    ]);

    echo $form->field($model, 'name');
    
    echo Html::submitButton('Dodaj', ['class' => 'btn btn-success']);

ActiveForm::end();

$js = <<<JS
$(function() {
    $('#modal-sm-title').html('Dodaj ulicę');

	$("#{$model->formName()}").on('beforeSubmit', function(e){
		
		var form = $(this);
	 	
		$.post(
	  		form.attr("action"),
	  		form.serialize()
	 	).done(function(result) {
	 		if(result == 1) {
				$('#modal-sm').modal('hide');
	 			$.pjax.reload({container: '#address-grid-pjax'});
	 		}
	 		else{
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