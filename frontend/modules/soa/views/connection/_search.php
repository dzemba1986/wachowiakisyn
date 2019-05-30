<?php

/**
 * @var yii\web\View $this
 * @var backend\models\ConnectionSearch $searchModel
 * @var yii\widgets\ActiveForm $form
 */ 

use backend\modules\address\models\Teryt;
use common\models\soa\ConnectionType;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>


    <?php $form = ActiveForm::begin([
        'action' => ['connection/index', 'mode' => 'all'],
        'method' => 'get',
    	'id' => 'global-search-form',
    ]); ?>    

    <div class="row">
        
        <?= $form->field($searchModel, 'street', [
                'options' => ['class' => 'col-md-3', 'style' => 'padding-left: 15px; padding-right: 5px;'], 
                'template' => "{input}\n{hint}\n{error}",
            ])->widget(Select2::className(), [
                'data' => ArrayHelper::map(AddressShort::findOrderStreetName(), 't_ulica', 'ulica'),
            	'options' => ['placeholder' => 'Ulica'],
            	'pluginOptions' => [
            		'allowClear' => true
            	],
        	])
        ?>

        <?= $form->field($searchModel, 'house', [
                'options' => ['class' => 'col-md-1', 'style' => 'padding-left: 5px; padding-right: 5px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('house')]) 
        ?>

        <?= $form->field($searchModel, 'flat', [
                'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 5px; padding-right: 5px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('flat')]) 
        ?>
        
        <?= $form->field($searchModel, 'house_detail', [
                'options' => ['class' => 'col-md-1', 'style' => 'padding-left: 5px; padding-right: 5px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('house_detail')]) 
        ?>

        <?= $form->field($searchModel, 'flat_detail', [
                'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('flat_detail')]) 
        ?>
        
        
        <?= $form->field($searchModel, 'type_id', [
                'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 5px; padding-right: 5px;'], 
                'template' => "{input}\n{hint}\n{error}",
            ])->dropDownList((ArrayHelper::map(ConnectionType::find()->all(), 'id', 'name')), ['prompt' => $searchModel->getAttributeLabel('type_id')]) 
        ?>
        
        <?= $form->field($searchModel, 'ara_id', [
                'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 5px; padding-right: 5px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('ara_id')]) 
        ?>
        
        <?= $form->field($searchModel, 'soa_id', [
                'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 5px; padding-right: 5px;'],
                'template' => "{input}\n{hint}\n{error}",
            ])->textInput(['placeholder' => $searchModel->getAttributeLabel('soa_id')]) 
        ?>
        
    </div>

    <div class="row">
	    <?= $form->field($searchModel, 'start_date', [
	        'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 15px; padding-right: 5px;'], 
	        'template' => "{input}\n{hint}\n{error}",
	    ])->widget(DatePicker::className(), [
	            'model' => $searchModel,                    
	            'attribute' => 'start_date',
	            'pickerButton' => false,
	    		'language'=>'pl',
	            'options' => ['id' => 'start', 'placeholder' => $searchModel->getAttributeLabel('start_date')],
	            'pluginOptions' => [
	            	'format' => 'yyyy-mm-dd',
	                'todayHighlight' => true,
	                'endDate' => '0d',
	            ]
	    ]) ?>
	
	    <?= $form->field($searchModel, 'conf_date', [
	        'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
	        'template' => "{input}\n{hint}\n{error}",
	    ])->widget(DatePicker::className(), [
	            'model' => $searchModel,
	            'attribute' => 'conf_date',
	            'pickerButton' => false,
	    		'language'=>'pl',
	            'options' => ['id' => 'conf', 'placeholder' => $searchModel->getAttributeLabel('conf_date')],
	            'pluginOptions' => [
	                'format' => 'yyyy-mm-dd',
	                'todayHighlight' => true,
	                'endDate' => '0d',
	            ]
	    ]) ?>
	
	    <?= $form->field($searchModel, 'pay_date', [
	        'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
	        'template' => "{input}\n{hint}\n{error}",
	    ])->widget(DatePicker::className(), [
	            'model' => $searchModel,
	            'attribute' => 'pay_date',
	            'pickerButton' => false,
	    		'language'=>'pl',
	            'options' => ['id' => 'pay', 'placeholder' => $searchModel->getAttributeLabel('pay_date')],
	            'pluginOptions' => [
	                'format' => 'yyyy-mm-dd',
	                'todayHighlight' => true,
	                'endDate' => '0d',
	            ]
	    ]) ?>
	
	    <?= $form->field($searchModel, 'phone_date', [
	        'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
	        'template' => "{input}\n{hint}\n{error}",
	    ])->widget(DatePicker::className(), [
	            'model' => $searchModel,
	            'attribute' => 'phone_date',
	            'pickerButton' => false,
	    		'language'=>'pl',
	            'options' => ['id' => 'move_phone', 'placeholder' => $searchModel->getAttributeLabel('phone_date')],
	            'pluginOptions' => [
	                'format' => 'yyyy-mm-dd',
	                'todayHighlight' => true,
	            ]
	    ]) ?>
	    
	    <?= $form->field($searchModel, 'close_date', [
	        'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
	        'template' => "{input}\n{hint}\n{error}",
	    ])->widget(DatePicker::className(), [
	            'model' => $searchModel,
	            'attribute' => 'close_date',
	            'pickerButton' => false,
	            'language'=>'pl',
	            'options' => ['id' => 'resignation', 'placeholder' => $searchModel->getAttributeLabel('close_date')],
	            'pluginOptions' => [
	                'format' => 'yyyy-mm-dd',
	                'todayHighlight' => true,
	                'endDate' => '0d',
	            ]
	    ]) ?>
    
    	<div style="padding-left: 5px; padding-right: 5px;" class="col-xs-1" ><?= Html::submitButton('Szukaj', ['class' => 'btn btn-danger', 'style' => 'width:100%']) ?></div>
    </div>
    <div class="row">
        
	    <?= $form->field($searchModel, 'nocontract', [
	        'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 15px; padding-right: 5px;'],
	    ])->checkbox(['uncheck' => null, 'checked' => 1]) ?>  
	        
	    <?= $form->field($searchModel, 'vip', [
	        'options' => ['class' => 'col-xs-1', 'style' => 'padding-left: 5px; padding-right: 5px;'],
	    ])->checkbox(['uncheck' => null, 'checked' => 1]) ?> 
    
    </div>
        
    <?php ActiveForm::end(); ?>