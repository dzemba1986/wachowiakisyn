<?php

use backend\modules\address\models\Teryt;
use common\models\crm\InstallTask;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\time\TimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\crm\InstallTask $task
 * @var backend\modules\address\models\Address $address
 * @var yii\widgets\ActiveForm $form
 */

$form = ActiveForm::begin([
    'id' => $task->formName(),
    'options' => ['style' => 'padding-left:10px;padding-right:10px'],
]);

    echo Html::tag('div', '', ['id' => 'error', 'class' => 'row alert alert-danger alert-dismissable', 'role' => 'alert', 'style' => 'display:none']);

    if ($address->isNewRecord) {
        
        echo Html::label('Adres zamieszkania', '', ['class' => 'row']);
        
        echo Html::beginTag('div', ['class' => 'row no-gutter']);
        
        echo $form->field($address, 't_ulica', [
            'options' => ['class' => 'col-sm-6']
        ])->label(false)->widget(Select2::class,[
            'data' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
            'options' => ['placeholder' => 'Ulica'],
        ]);
        
        echo $form->field($address, 'dom', [
            'options' => ['class' => 'col-md-2']
        ])->textInput(['placeholder' => $address->getAttributeLabel('dom')])->label(false);
    		
    	echo $form->field($address, 'lokal', [
        	'options' => ['class' => 'col-md-2']
        ])->textInput(['placeholder' => $address->getAttributeLabel('lokal')])->label(false);
    			
        echo $form->field($address, 'dom_szczegol', [
        	'options' => ['class' => 'col-md-2']    		
        ])->textInput(['placeholder' => $address->getAttributeLabel('dom_szczegol')])->label(false);
        
        echo Html::endTag('div');
    } else
        echo Html::tag('h4', $address->toString(), ['style' => 'text-align: center']);
    
    echo Html::label('Data i czas wykonania', 'installtask-day', ['class' => 'row']);
    
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
    	        'showMeridian' => false
    	    ]
    	]);;
    	
    	echo $form->field($task, 'end_time', [
    	    'options' => ['class' => 'col-md-3']
    	])->label(false)->widget(TimePicker::class, [
    	    'pluginOptions' => [
    	        'showMeridian' => false
    	    ]
    	]);
	
	echo Html::endTag('div');
	
	echo Html::beginTag('div', ['class' => 'row no-gutter']);
	
    	echo $form->field($task, 'category_id', [
    	    'options' => ['class' => 'col-md-3']
    	])->dropDownList(InstallTask::$categoryName, ['prompt' => 'Wybierz...']);
    	
    	echo $form->field($task, 'phone', ['options' => ['class' => 'col-md-3']]);
    	
    	echo $form->field($task, 'payer', [
    	    'options' => ['class' => 'col-md-3']
    	])->dropDownList(InstallTask::$payerName, ['prompt' => 'Wybierz...']);

    	echo $form->field($task, 'receive_by', [
    	    'options' => ['class' => 'col-md-3']
    	])->dropDownList(InstallTask::$receiveName, ['prompt' => 'Wybierz...']);
    	
	echo Html::endTag('div');
	
	echo Html::beginTag('div', ['class' => 'row']);
	
	   echo $form->field($task, 'desc')->textarea(['rows' => '4', 'style' => 'resize: vertical', 'maxlength' => 1000, 'placeholder' => 'Dodaj przybliżony koszt']);
	
	echo Html::endTag('div');

	echo Html::beginTag('div', ['class' => 'form-group']);
    
	echo Html::submitButton('Dodaj', ['class' => 'row btn btn-success']);
    
    echo Html::endTag('div');

ActiveForm::end();

$js = <<<JS
$(function() {
    $('#modal-task-title').html('Dodaj montaż'); //dodanie przez LP
    $('#modal-title').html('Dodaj montaż'); //dodanie przez kalendarz

	$('#{$task->formName()}').on('beforeSubmit', function(e) {
	 	$.post(
	  		$(this).attr("action"),
	  		$(this).serialize()
	 	).done(function(result) {
	 		if (result[0] == 1) {
	 			$(this).trigger('reset');
				$('#modal-task').modal('hide'); //dodanie przez LP
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