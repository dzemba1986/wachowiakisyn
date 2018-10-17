<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="connection-search">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
    	'id' => 'global-search',
    ]); ?>    
    
    <div class="row">
	    <?= $form->field($searchModel, 'minConfDate', [
	        'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 15px; padding-right: 5px;'], 
	        'template' => "{input}\n{hint}\n{error}",
	    ])->widget(DatePicker::className(), [
	            'model' => $searchModel,                    
	            'attribute' => 'minConfDate',
	            'pickerButton' => false,
	            'options' => ['placeholder' => 'Od'],
	    		'language'=>'pl',
	            'pluginOptions' => [
	                'format' => 'yyyy-mm-dd',
	                'todayHighlight' => true,
	                'endDate' => '0d',
	            ]
	    ]) ?>
	
	    <?= $form->field($searchModel, 'maxConfDate', [
	        'options' => ['class' => 'col-md-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
	        'template' => "{input}\n{hint}\n{error}",
	    ])->widget(DatePicker::className(), [
	            'model' => $searchModel,
	            'attribute' => 'maxConfDate',
	            'pickerButton' => FALSE,
	            'options' => ['placeholder' => 'Do'],
	    		'language'=>'pl',
	            'pluginOptions' => [
	                'format' => 'yyyy-mm-dd',
	                'todayHighlight' => true,
	                'endDate' => '0d',
	            ]
	    ]) ?>
	    
	    <?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary col-xs-1']) ?>
    </div>
        
    <?php ActiveForm::end(); ?>
        
</div>