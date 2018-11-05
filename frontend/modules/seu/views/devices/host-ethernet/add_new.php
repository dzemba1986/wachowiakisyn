<?php
use common\models\seu\network\Vlan;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var frontend\modules\seu\models\forms\AddHostEthernetForm $model
 * @var common\models\soa\Connection $connection
 * @var \yii\widgets\ActiveForm $form
 * @var array $jsonType
 */

$form = ActiveForm::begin([
	'id' => $model->formName(),
    'validationUrl' => Url::to(['add-host-validation']),
    'options' => ['style' => 'padding-left:10px;padding-right:10px']
]);
?>

	<div class="row no-gutter">
    	
    	<?= $form->field($model, 'deviceId', [
    	    'template' => "{label} [ <span class='ssh'></span> ] \n{input}{error}{hint}",
    	    'options' => ['class' => 'col-sm-6'],
    	])->widget(Select2::classname(), [
    		'language' => 'pl',
           	'options' => [
           	    'placeholder' => 'Urządzenie nadrzędne',
           		'onchange' => new JsExpression("
					$.get('" . Url::to(['link/list-port']) . "&deviceId=' + $(this).val(), function(data) {
						$('select[name=\"AddHostEthernetForm[port]\"]').html(data);
					});
                    $.get('" . Url::to(['swith/get-ip-link']) . "&id=' + $(this).val(), function(data) {
						$('.ssh').html(data);
					});
				")
            ],
	    	'pluginOptions' => [
	    		'allowClear' => true,
	    		'minimumInputLength' => 1,
	    		'language' => [
	    			'errorLoading' => new JsExpression("function () { return 'Proszę czekać...'; }"),
	    		],
	    	    'ajax' => [
	    	        'url' => Url::to(['device/list-from-tree']),
	    	        'dataType' => 'json',
	    	        'data' => new JsExpression("function(params) {
    					return {
    						q : params.term,
                            type : " . $jsonType . "
						};
					}")
	    	    ],
		    	'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		    	'templateResult' => new JsExpression('function(device) { return device.concat; }'),
		    	'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
	    	]
    	]) ?>
    
    	<?= $form->field($model, 'port', [
    	    'options' => ['class' => 'col-sm-3'],
    	])->dropDownList([]) ?>
    	
    	<?= $form->field($model, 'mac', [
    	    'enableAjaxValidation' => true,
    	    'options' => ['class' => 'col-sm-3'],
    	]) ?>
	
	</div>
	
	<div class="row no-gutter">
        
        <?= $form->field($model, 'vlanId', [
            'options' => ['class' => 'col-sm-3'],
        ])->dropDownList(ArrayHelper::map(Vlan::find()->select('id')->orderBy('id')->all(), 'id', 'id'), [
        	'prompt' => 'Vlan',
        	'onchange' => new JsExpression("
        		$.get('" . Url::toRoute('subnet/list') . "&vlanId=' + $(this).val(), function(data){
            		$('select[name=\"AddHostEthernetForm[subnetId]\"]').html(data).trigger('change');
        		});	
        	")  	
        ]) ?>
        
        <?= $form->field($model, 'subnetId', [
            'options' => ['class' => 'col-sm-5'],
        ])->dropDownList([], [
        	'prompt' => 'Podsieć',
        	'onchange' => new JsExpression("
        		$.get('" . Url::toRoute('ip/select-list') . "&subnet=' + $(this).val() + '&mode=free', function(data){
    				$('select[name=\"AddHostEthernetForm[ip]\"]').html(data);
    			});
        	")  	
        ]) ?>
        
        <?= $form->field($model, 'ip', [
            'options' => ['class' => 'col-sm-4'],
        ])->dropDownList([], [
        	'prompt' => 'Ip',  	
        ]) ?>
        
	</div>

	<div class="row no-gutter">
	
		<?= Html::submitButton('Dodaj', ['class' => 'btn btn-primary']) ?>

	</div>
	
<?php ActiveForm::end() ?>	

<?php
$deviceId = json_encode($model->deviceId);
$port = json_encode($model->port);
$deviceListUrl = Url::to(['device/list-from-tree', 'id' => $model->deviceId]);
$portListUrl = Url::to(['link/list-port', 'deviceId' => $model->deviceId, 'selected' => $model->port]);
$ipLinkUrl = Url::to(['swith/get-ip-link']);

$js = <<<JS
$(function() {
    var deviceId = {$deviceId}; //jeżeli urządzenie jest ustawione pobiera jego wartość id
    var port = {$port}; //jeżeli port jest ustawiony pobiera jego wartość

    $('.modal-header h4').html('{$model->address}');

    if (deviceId) {
		$.getJSON('{$deviceListUrl}', function(data) {
			$('#select2-addhostethernetform-deviceid-container').html(data.results.concat);
		});

        $.get('{$ipLinkUrl}&id={$deviceId}', function(data){
            $('.ssh').html(data);
        });
	
        if (port !== null) {
            $.get('{$portListUrl}', function(data) {
                $('select[name="AddHostEthernetForm[port]"]').html(data);
            });
        }
	}

    $('#{$model->formName()}').on('beforeSubmit', function(e) {
		var form = $(this);
     	$.post(
      		form.attr('action'),
      		form.serialize()
     	).done(function(result){
 			if(result){
                //window.location.replace(result);
 			}
 			else{
 				alert(result);
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