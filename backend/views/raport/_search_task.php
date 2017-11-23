<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\models\Address;
use backend\models\Type;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConnectionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="connection-search">
    <?php $form = ActiveForm::begin([
        'action' => ['raport/task'],
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
            //'removeButton' => FALSE,
            'language'=>'pl',
            'options' => ['placeholder' => 'Od'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'endDate' => '0d', //wybór daty max do dziś
            ]
    ]) ?>

    <?= $form->field($searchModel, 'maxClose', [
        'options' => ['class' => 'col-xs-2', 'style' => 'padding-left: 5px; padding-right: 5px;'],
        'template' => "{input}\n{hint}\n{error}",
    ])->widget(DatePicker::className(), [
            'model' => $searchModel,
            'attribute' => 'maxClose',
            //'removeButton' => FALSE,
            'language'=>'pl',
            'options' => ['placeholder' => 'Do'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'endDate' => '0d', //wybór daty max do dziś
            ]
    ]) ?>
    
    <?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary col-xs-1']) ?>
    </div>
        
    <?php ActiveForm::end(); ?>
        
</div>