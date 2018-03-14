<?php
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 */

$form = ActiveForm::begin([
	'id' => 'replace'
]); ?>

	<?= Html::label('Wybierz urządzenie z magazynu') ?>
	
	<?= Select2::widget([
			'id' => 'device-select',
			'name' => 'destinationDeviceId',
    		'language' => 'pl',
            'options' => [
            	'placeholder' => 'Urządzenie nadrzędne',
            	'onchange' => new JsExpression("
                    $.get('" . Url::to(['tree/replace-port']) . "&sourceDeviceId={$deviceId}&destinationDeviceId=' + $(this).val(), function(data){
				    	$('#port-select').html(data);
                    });
        	    "),
            ],
    		'pluginOptions' => [
    			'allowClear' => true,
    			'minimumInputLength' => 2,
    			'language' => [
    				'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
    			],
    			'ajax' => [
    				'url' => Url::to(['device/list-from-store']),
    				'dataType' => 'json',
    				'data' => new JsExpression('function(params) { 
    					return {
    						q : params.term,
						}; 
					}')
	    		],
	    		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
	    		'templateResult' => new JsExpression('function(device) { return device.concat; }'),
	    		'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
    		]
    	])     
    ?>
    
    <div id="port-select"></div>
    
<?php ActiveForm::end(); ?>

<?php
$urlView = Url::to(['device/tabs-view']);

$js = <<<JS
$(function(){
    $('#replace').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1){
				$('#modal-replace').modal('hide');
                var tree = $("#device_tree").jstree(true);
                tree.refresh();
                $('#device_desc').load('{$urlView}&id=' + $('#device-select').val());  
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