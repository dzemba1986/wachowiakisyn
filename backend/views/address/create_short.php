<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Address;
use app\models\Ulic;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

/**
 * @var $this yii\web\View 
 * @var $model backend\models\Address
 * @var $form yii\widgets\ActiveForm
 */ 
?>

<div id='message'></div>

<?php $form = ActiveForm::begin([
		'id'=>$model->formName()
	]
); ?>
	<?= $form->field($model, 't_ulica')
	->widget(Select2::className(), [
    		'language' => 'pl',
    		'pluginOptions' => [
    			'allowClear' => true,
    			'minimumInputLength' => 1,
    			'language' => [
					'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
    			],
    			'ajax' => [
    				'url' => Url::to(['address/teryt-list']),
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
        ])
    ?>

	<div class="row">
	
    <?= $form->field($model, 'name', [
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
    ]) ?>
    
    <?= $form->field($model, 'config', [
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
    ])->dropDownList([1 => 'Winogrady', 2 => 'Reszta'], ['prompt' => '']) ?>
    
    </div>
    
    <div class="row">
    
    <?= $form->field($model, 'ulica_prefix', [
    	'options' => ['class' => 'col-md-3', 'style' => 'padding-right: 5px;']
    ])->hiddenInput()->label(false) ?>
    
    <?= $form->field($model, 'ulica', [
    	'options' => ['class' => 'col-md-9', 'style' => 'padding-left: 5px;']
    ])->hiddenInput()->label(false) ?>

	</div>
    
    <div class="row">
    
    <?= $form->field($model, 't_gmi', [
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-right: 5px;']
    ])->hiddenInput()->label(false) ?>
    
    <?= $form->field($model, 't_miasto', [
    	'options' => ['class' => 'col-md-6', 'style' => 'padding-left: 5px;']
    ])->hiddenInput()->label(false) ?>

	</div>
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Edytuj', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$(function(){

	$("#{$model->formName()}").on('beforeSubmit', function(e){
		
		var form = $(this);
	 	
		$.post(
	  		form.attr("action"),	//url
	  		form.serialize()	//dane
	 	).done(function(result){
	 		if(result == 1){
				$('#modal-update').modal('hide');
	 			$.pjax.reload({container: '#address-grid-pjax'});
	 		}
	 		else{
	 			$('div#message').html("<h3 style='color:red;'>" + result + "</h3>");
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