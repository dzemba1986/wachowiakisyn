<?php
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
	'id' => 'replace'
]); ?>

	<?= Html::label('Wybierz urządzenie z magazynu') ?>
	
	<?= Select2::widget([
			'id' => 'device-select',
			'name' => 'device',
    		'language' => 'pl',
            'options' => [
            	'placeholder' => 'Urządzenie nadrzędne',
            	'onchange' => new JsExpression("
                    $.get('" . Url::toRoute('tree/replace-port') . "&deviceSourceId={$deviceId}&deviceDestinationId=' + $(this).val(), function(data){
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
    				'url' => Url::toRoute('device/list-from-store'),
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
    
    <div id="port-select" style="display: table; width: 100%"></div>
    
    <div class="form-group" style="display: table-row;">
		<?= Html::submitButton('Zamień', ['id' => 'change', 'class' => 'btn btn-primary']); ?>
	</div>

<?php ActiveForm::end(); ?>

<script>
$(function(){
	$('#replace-from-store-form').on('beforeSubmit', function(e){

		var tree = $("#device_tree").jstree(true);
		var map = {};

		$("#destination").children().each(function(index, element){
			$(element).children( "div" ).length ? map[$(element).children("div").attr('id')] = element.id : null;
			//console.log(element.id + " - " + $(element).children("div").attr('id'));
		});
        //console.log(map);

        if($("#source").has("div").length) 
            alert("Zostało " + $("#source").children("div").length + " elementów");
        else {
            
			$.post(
				$(this).attr("action"),
				{ 
					map, 
					'deviceDestination' : $('#device-select').val()
				} 
			).done(function(result){
		 		if(result == 1){
		 			$("#modal-replace-store").modal("hide");
		 			tree.refresh();
		 		}
		 		else{
				
		 			$('#message').html(result);
		 		}
		 	}).fail(function(){
		 		console.log('server error');
		 	});
        }
    
        return false;
	});
});
</script>