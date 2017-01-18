<?php
use backend\models\Device;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

$form = ActiveForm::begin([
	'id' => 'replace-from-store-form'
]); ?>

	<?= Html::label('Wybierz urządzenie z magazynu') ?>
	
	<?= Select2::widget([
			'id' => 'device-select',
			'name' => 'device',
    		'language' => 'pl',
            'options' => [
//             	'class' => 'col-md-5', 
//             	'style' => 'padding-right: 5px;',
            	'placeholder' => 'Urządzenie nadrzędne',
            	'onchange' => '
                	$.get( "' . Url::toRoute('tree/replace-device-port-select') . '&deviceSource=' . $device . '" + "&deviceDestination=" + $(this).val(), function(data){
						$("#port-select").html(data);
					} );
                '
            ],
    		'pluginOptions' => [
    			
    			'allowClear' => true,
    			'minimumInputLength' => 2,
    			'language' => [
    				
    				'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
    			],
    			'ajax' => [
    				'url' => Url::toRoute('device/list'),
    				'dataType' => 'json',
    				'data' => new JsExpression('function(params) { 
    					return {
    						q : params.term,
    						type : [1, 2, 3, 4, 6, 8],
    						store : true
						}; 
					}')
	    		],
	    		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
	    		'templateResult' => new JsExpression('function(device) { return device.concat; }'),
	    		'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
    		]
    	])     
    ?>
    
    <div id="port-select" style="display: table;"></div>
    
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