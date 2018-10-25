<?php
use kartik\growl\GrowlAsset;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var integer $parentId
 * @var \yii\web\View $this
 * @var frontend\modules\seu\models\forms\AddDeviceOnTreeForm $model
 */

GrowlAsset::register($this);

$form = ActiveForm::begin([
    'id' => $model->formName(),
    'options' => ['style' => 'padding-left:10px;padding-right:10px']
]); ?>

	<div class="row no-gutter">
	
		<?= $form->field($model, 'parentId')->hiddenInput(['value' => $parentId])->label(false); ?>
	
		<?= Html::label('Wybierz urządzenie i porty', null, ['class' => 'col-md-5']) ?>
	
	</div>

	<div class="row no-gutter">
	
		<?= $form->field($model, 'childId', [
    		'options' => ['class' => 'col-md-12'],
    	])->label(false)->widget(Select2::classname(), [
    		'language' => 'pl',
            'options' => [
                'placeholder' => 'Urządzenie',
                'onchange' => new JsExpression("
                    $.get('" . Url::to(['link/list-port']) . "&deviceId=' + $(this).val() + '&mode=all', function(data){
						$( 'select[name=\'AddDeviceOnTreeForm[childPort]\']' ).html(data);
					});
                
                    $.get('" . Yii::$app->request->url . "&childId=' + $(this).val(), function(data){
				    	$( '#add-form' ).html(data);
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
    	            'data' => new JsExpression("function(params) {
        					return {
        						q : params.term,
        					};
        				}")
    	        ],
    	        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
    	        'templateResult' => new JsExpression('function(device) { return device.concat; }'),
    	        'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
    	    ]
    	])?>
    	
	</div>
	
	<div class="row no-gutter">
	
		<?= $form->field($model, 'parentPort', [
    	   'options' => ['class' => 'col-md-6']
        ])->dropDownList([], [
            'prompt'=>'port rodzica',
            'onfocus' => new JsExpression("
                $.get('" . Url::to(['link/list-port']) . "&deviceId=" . $parentId . "', function(data){
					$( 'select[name=\'AddDeviceOnTreeForm[parentPort]\']' ).html(data);
				});
    	    "),
        ])->label(false); ?>
        
        <?= $form->field($model, 'childPort', [
    	   'options' => [
    	       'class' => 'col-md-6',
    	   ]
        ])->dropDownList([], ['prompt' => 'port urządzenia'])->label(false); ?>	
	
	</div>	
	
    <div id="add-form"></div>
    
<?php ActiveForm::end(); ?>

<?php
$urlView = Url::to(['tabs-view']);

$js = <<<JS
$(function(){
    $('.modal-header h4').html('Dodaj urządzenie');

    $('#{$model->formName()}').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if(result == 1) {
				$('#modal').modal('hide');
                var tree = $("#device_tree").jstree(true);
                tree.refresh();
                $('#device_desc').load('{$urlView}&id=' + $('#device-select').val());
                $.notify('Dodano urządzenie.', {
                    type: 'success',
                    placement : { from : 'top', align : 'right'},
                });  
	 		}
	 		else{
                $('#modal').modal('hide');
                $.notify('Błąd dodania urządzenia.', {
                    type: 'danger',
                    placement : { from : 'top', align : 'right'}, 
                });
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