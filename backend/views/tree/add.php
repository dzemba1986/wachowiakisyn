<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use backend\models\Tree;
use backend\models\Address;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use backend\models\Device;
use yii\web\JsExpression;

echo DetailView::widget([
	'model' => $modelDevice,
    'attributes' => [
    	[
    		'attribute' => 'type',
    		'value' => $modelDevice->modelType->name,	
    	],
    	[
    		'attribute' => 'model',
    		'value' => $modelDevice->modelModel->name,
    	],
     	'mac',
        'serial',
	],
]);
?>

<?php $form = ActiveForm::begin([
	'id' => 'update-device-form',
    ])?>
    
    <?= Html::label('Lokalizacja') ?>
    
    <div class="row">
    
    <?= $form->field($modelAddress, 'ulica', [
			'options' => ['class' => 'col-md-5', 'style' => 'padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->widget(Select2::className(), [
     		'data' => ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica'),
       		'options' => ['placeholder' => 'Ulica'],
       		'pluginOptions' => [
            	'allowClear' => true
            ],
        ])
    ?>
    
    <?= $form->field($modelAddress, 'dom' , [
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->textInput(['placeholder' => $modelAddress->getAttributeLabel('dom')]) 
    ?>
    
    <?= $form->field($modelAddress, 'dom_szczegol' , [
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->textInput(['placeholder' => $modelAddress->getAttributeLabel('dom_szczegol')]) 
    ?>
    
    <?= $form->field($modelAddress, 'pietro' , [
    		'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList(Address::getFloor(), ['prompt' => $modelAddress->getAttributeLabel('pietro')]) 
    ?>
    
    </div>
    
    <?= Html::label('Urządzenie i porty') ?>
    
    <div class="row">

    <?= $form->field($modelTree, 'parent_device', [
    		'options' => ['class' => 'col-md-5', 'style' => 'padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->widget(Select2::classname(), [
    		'language' => 'pl',
            'options' => [
            	'placeholder' => 'Urządzenie nadrzędne',
            	'onchange' => '
                	$.get( "' . Url::toRoute('tree/free-port-list') . '&id=" + $("select#tree-parent_device").val(), function(data){
						$("select#tree-parent_port").html(data);
					} );
                                        		
                    $.get( "' . Url::toRoute('tree/free-port-list') . '&id=" + "' . Yii::$app->request->get("id") . '", function(data){
						$("select#tree-port").html(data);
					} );
                                        		'
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
    
    <?= $form->field($modelTree, 'parent_port', [
    		'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList([''], ['prompt' => $modelTree->getAttributeLabel('parent_port')]) 
    ?>
    
    <?= $form->field($modelTree, 'port', [
    		'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 5px; padding-right: 5px;'],
    		'template' => "{input}\n{hint}\n{error}",
    	])->dropDownList([''], ['prompt' => $modelTree->getAttributeLabel('port')]) 
    ?>
        
    </div>
    
	<?= Html::submitButton($modelDevice->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>