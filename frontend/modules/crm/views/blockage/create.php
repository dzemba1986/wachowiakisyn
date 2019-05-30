<?php

use common\models\crm\Blockage;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\crm\Blockage $task
 * @var yii\widgets\ActiveForm $form
 */

$form = ActiveForm::begin([
    'id' => $task->formName(),
    'options' => ['style' => 'padding-left:10px;padding-right:10px'],
]);

    echo Html::tag('div', '', ['id' => 'error', 'class' => 'row alert alert-danger alert-dismissable', 'role' => 'alert', 'style' => 'display:none']);

    echo Html::label('Data i czas blokady/rezerwacji', '', ['class' => 'row']);
    
    echo Html::beginTag('div', ['class' => 'row no-gutter']);
        
        echo $form->field($task, 'day', [
            'options' => ['class' => 'col-md-6']
        ])->label(false)->widget(DatePicker::class, [
        	'type' => DatePicker::TYPE_COMPONENT_APPEND,
        	'language' => 'pl',
        	'pluginOptions' => [
            	'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
            ]
        ]);
    		
    	echo $form->field($task, 'start_time', [
    	    'options' => ['class' => 'col-md-3']
    	])->label(false)->widget(TimePicker::class, [
    	    'pluginOptions' => [
    	        'showMeridian' => false,
    	    ]
    	]);;
    	
    	echo $form->field($task, 'end_time', [
    	    'options' => ['class' => 'col-md-3']
    	])->label(false)->widget(TimePicker::class, [
    	    'pluginOptions' => [
    	        'showMeridian' => false,
    	    ]
    	]);
	
	echo Html::endTag('div');
	
	echo Html::beginTag('div', ['class' => 'row no-gutter']);
	
    	echo $form->field($task, 'category_id', [
    	    'options' => ['class' => 'col-md-3']
    	])->dropDownList(Blockage::$categoryName, ['prompt' => 'Wybierz...']);
    	
    	echo $form->field($task, 'receive_by', [
    	    'options' => ['class' => 'col-md-3']
    	])->dropDownList(Blockage::$receiveName, ['prompt' => 'Wybierz...']);
	
	echo Html::endTag('div');
	
	echo Html::beginTag('div', ['class' => 'row']);
	
	   echo $form->field($task, 'desc')->textarea(['rows' => '4', 'style' => 'resize: vertical', 'maxlength' => 1000, 'placeholder' => 'Podaj przyczynę blokady/rezerwacji']);
	
	echo Html::endTag('div');
    
	echo Html::submitButton('Dodaj', ['class' => 'row btn btn-success']);
    

ActiveForm::end();

$js = <<<JS
$(function() {
    $('#modal-title').html('Dodaj blokadę/rezerwację');

	$('#{$task->formName()}').on('beforeSubmit', function(e) {
	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result) {
	 		if (result[0] == 1) {
	 			$(this).trigger('reset');
				$('#modal').modal('hide'); //dodanie przez kalendarz
				$('#calendar').fullCalendar('refetchEvents');
	 		} else {
                $('#error').html(result[1]);
	 			$('#error').fadeTo(2500, 500).slideUp(500, function() {
                    $('#error').slideUp(500);
                });
	 		}
	 	}).fail(function() {
	 		$('#error').html('Problem z wysłaniem żądania');
 			$('#error').fadeTo(2500, 500).slideUp(500, function() {
                $('#error').slideUp(500);
            });
	 	});
		return false;				
	});
});
JS;

$this->registerJs($js);
?>