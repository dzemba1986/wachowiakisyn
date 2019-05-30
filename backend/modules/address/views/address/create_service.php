<?php

use backend\modules\address\models\Teryt;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var $this yii\web\View 
 * @var $service backend\modules\address\models\Service
 * @var $form yii\widgets\ActiveForm
 */ 

$form = ActiveForm::begin([
	'id' => $service->formName(),
    'options' => ['style' => 'padding-left:10px;padding-right:10px'],
]
); 

    echo Html::label('Adres (przedział)', '');

    echo Html::beginTag('div', ['class' => 'row no-gutter']);
    
        echo $form->field($service, 't_ulica', [
        	'options' => ['class' => 'col-md-5']
        ])->widget(Select2::class,[
            'data' => ArrayHelper::map(Teryt::findOrderStreetName(), 't_ulica', 'ulica'),
            'options' => ['placeholder' => 'Ulica'],
        ])->label(false);
    
        echo $form->field($service, 'dom', [
        	'options' => ['class' => 'col-md-2']
        ])->label(false);
        
        echo $form->field($service, 'dom_szczegol', [
        	'options' => ['class' => 'col-md-1']
        ])->textInput(['placeholder' => 'Kl.'])->label(false);
        
        echo $form->field($service, 'lokal_od', [
        	'options' => ['class' => 'col-md-2']
        ])->textInput(['placeholder' => 'Lokal od'])->label(false);
        
        echo $form->field($service, 'lokal_do', [
        	'options' => ['class' => 'col-md-2']
        ])->textInput(['placeholder' => 'Lokal do'])->label(false);
        
    echo Html::endTag('div');
    
    echo Html::beginTag('div', ['class' => 'row no-gutter']);
        
        $infra_options = ['' => '', -1 => 'TODO', 0 => 0, 1 => 1, 2 => 2];
        
        echo $form->field($service, 'utp', [
            'options' => ['class' => 'col-md-2']
        ])->dropDownList($infra_options);
        
        echo $form->field($service, 'utp_cat3', [
        	'options' => ['class' => 'col-md-2']
        ])->dropDownList($infra_options);
        
        echo $form->field($service, 'coax', [
        	'options' => ['class' => 'col-md-2']
        ])->dropDownList($infra_options);

        echo $form->field($service, 'optical_fiber', [
        	'options' => ['class' => 'col-md-2']
        ])->dropDownList($infra_options);
    
    echo Html::endTag('div');
    
    $service_options = ['', '', ''];
    echo Html::beginTag('div', ['class' => 'row no-gutter']);
        
        echo Html::beginTag('fieldset');
            
            echo Html::tag('legend', 'Internet');
            
            echo $form->field($service, 'net_utp', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);
            
            echo $form->field($service, 'net_optical_fiber', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options, ['inline'=>true]);
            
            echo $form->field($service, 'netx_utp', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);
            
            echo $form->field($service, 'netx_optical_fiber', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);
        
            echo Html::endTag('fieldset');
            
    echo Html::endTag('div');

    echo Html::beginTag('div', ['class' => 'row no-gutter']);
        
        echo Html::beginTag('fieldset');
        
            echo Html::tag('legend', 'Telefon');
            
            echo $form->field($service, 'phone_utp', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'phone_utp_cat3', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);
            
        echo Html::endTag('fieldset');
    
    echo Html::endTag('div');
    
    echo Html::beginTag('div', ['class' => 'row no-gutter']);
        
        echo Html::beginTag('fieldset');
        
            echo Html::tag('legend', 'Telewizja');
            
            echo $form->field($service, 'hfc', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'iptv_utp', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'iptv_optical_fiber', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'rfog', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);
            
        echo Html::endTag('fieldset');
        
    echo Html::endTag('div');
    
    echo Html::beginTag('div', ['class' => 'row no-gutter']);
        
        echo Html::beginTag('fieldset');
        
            echo Html::tag('legend', 'Pakiety');
            
            echo $form->field($service, 'iptv_net_utp', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'iptv_net_optical_fiber', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'iptv_netx_utp', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'iptv_netx_optical_fiber', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'rfog_net', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);

            echo $form->field($service, 'rfog_netx', [
                'options' => ['class' => 'col-md-4']
            ])->inline(true)->radioList($service_options);
            
        echo Html::endTag('fieldset');
        
    echo Html::endTag('div');

    echo Html::submitButton('Dodaj', ['class' => 'btn btn-success']);

ActiveForm::end();

$js = <<<JS
$(function() {
    $('#modal-title').html('Dodaj usługi na adresie');

	$("#{$service->formName()}").on('beforeSubmit', function(e){
		
		var form = $(this);
	 	
		$.post(
	  		form.attr("action"),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1) {
				$('#modal').modal('hide');
	 			$.pjax.reload({container: '#service-grid-pjax'});
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