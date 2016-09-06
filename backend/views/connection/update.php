<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $modelConnection backend\models\Connection */

$this->title = 'Edycja: ' . ' ' . $modelConnection->modelAddress->fullAddress;
$this->params['breadcrumbs'][] = ['label' => 'Umowy', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Edycja';
$this->params['breadcrumbs'][] = ['label' => $modelConnection->modelAddress->fullAddress];

$this->params['menu']=[
		['label'=>'Usuń umowę', 'url' => ['delete', 'id'=>$modelConnection->id], 'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
		],
		],
		//['label'=>'Montaż', 'url' => ['task/view-calendar', 'connectionId'=>$modelConnection->id]],
];

?>

<!-------------------------------------------- dodaj na drzewo okno modal --------------------------------------------->

	<?php Modal::begin([
		'id' => 'modal_add_tree',	
		'header' => '<center><h4>Dodaj do drzewa</h4></center>',
		'size' => 'modal-mg',
		'options' => [
			'tabindex' => false // important for Select2 to work properly
		],
	]);
	
	echo "<div id='modal_content_add_tree'></div>";
	
	Modal::end(); ?>

<!--------------------------------------------------------------------------------------------------------------------->

<div class="connection-update col-md-6">

    <div style="float:left; width:90%">
    	<?php $form = ActiveForm::begin([
            'id' => 'update-connection-form',
    	])?>
        
        <div style="display: flex">
		    
		    <?= $form->field($modelConnection, 'phone', [
		    	'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;'],
		    ]) ?>
		    
		    <?= $form->field($modelConnection, 'phone2', [
		    	'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 3px; padding-right: 0px;'],
		    ]) ?>
		
		</div>
		
		<div style="display: flex">
            
            <?= $form->field($modelConnection, 'pay_date', [
				'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			])->widget(DatePicker::className(), [
            	'model' => $modelConnection,
                'attribute' => 'pay_date',
                'language'=>'pl',
				'removeButton' => FALSE,
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d', //wybór daty max do dziś
                ]
            ])?>
            
            <?= $form->field($modelConnection, 'close_date', [
				'options' => ['class' => 'col-sm-4', 'style' => 'color : red; padding-left: 3px; padding-right: 0px;'],
			])->widget(DatePicker::className(), [
            	'model' => $modelConnection,
                'attribute' => 'close_date',
                'language'=>'pl',
				'removeButton' => FALSE,
                'pluginOptions' => [
                	'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'endDate' => '0d', //wybór daty max do dziś
                ]
            ])?>
		
		</div>
		
		<div style="display: flex">
		
			<?= $form->field($modelConnection, 'mac', [
				'options' => ['class' => 'col-sm-4', 'style' => 'padding-left: 0px; padding-right: 3px;'],
			]) ?>
			
			<?= $form->field($modelConnection, 'device', [
    			'options' => ['class' => 'col-sm-6', 'style' => 'padding-left: 3px; padding-right: 3px;'],
    		])->widget(Select2::classname(), [
    			//'initValueText' => 'test',
    			'language' => 'pl',
            	'options' => [
            		'placeholder' => 'Urządzenie nadrzędne',
            		'onchange' => new yii\web\JsExpression("

						$.get('" . Url::toRoute('tree/select-list-port') . "&device=' + $(this).val() + '&type=free', function(data){
							$('#connection-port').html(data).val('" . $modelConnection->port . "');
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
	    				'url' => Url::toRoute('device/list'),
	    				'dataType' => 'json',
	    				'data' => new JsExpression('function(params) { return {
	    					q:params.term, 
	    						type: 2, 
	    						dist:false
						}; }')
		    		],
		    		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		    		'templateResult' => new JsExpression('function(device) { return device.concat; }'),
		    		'templateSelection' => new JsExpression('function (device) { return device.concat; }'),
	    		]
    		]) ?>
    		
			<?= $form->field($modelConnection, 'port', [
				'options' => ['class' => 'col-sm-2', 'style' => 'padding-left: 3px; padding-right: 0px;'],
			])->dropDownList([], ['prompt'=>'port']) ?>
			
		</div>
        
		<?= $form->field($modelConnection, 'info')->textarea(['style' => 'resize: vertical']) ?>
        
        <?= $form->field($modelConnection, 'info_boa')->textarea(['style' => 'resize: vertical']) ?>
        
        <?= Html::submitButton($modelConnection->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
        
        <?php ActiveForm::end() ?>
        
    </div>	
</div>

<script>

$(function(){

	$("a[title='Zamontuj']").click(function(event){
        
		$('#modal_add_tree').modal('show')
			.find('#modal_content_add_tree')
			.load($(this).attr('href'));
    
        return false;
	});
	
	$.getJSON('<?= Url::toRoute(['device/list', 'id' => $modelConnection->device])?>', function(data){
		$('#select2-connection-device-container').html(data.results.concat);
	});

	$("#connection-device").trigger("change");
})

</script>