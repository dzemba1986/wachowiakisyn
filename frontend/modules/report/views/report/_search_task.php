<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var InstallTaskSearch $searchModel 
 * @var ActiveForm $form
 */
?>

<div class="connection-search">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
    	'id' => 'global-search',
    ]); ?>    
    
    <div class="row">
    <?= $form->field($searchModel, 'minClose', [
        'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 15px; padding-right: 5px;'], 
        'template' => "{input}\n{hint}\n{error}",
    ])->widget(DatePicker::className(), [
            'model' => $searchModel,                    
            'attribute' => 'minClose',
            'pickerButton' => false,
            'options' => ['placeholder' => 'Od'],
    		'language'=>'pl',
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'endDate' => '0d',
            ]
    ]) ?>

    <?= $form->field($searchModel, 'maxClose', [
        'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
        'template' => "{input}\n{hint}\n{error}",
    ])->widget(DatePicker::className(), [
            'model' => $searchModel,
            'attribute' => 'maxClose',
            'pickerButton' => false,
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