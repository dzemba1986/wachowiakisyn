<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Address;

/* @var $this yii\web\View */
/* @var $model backend\models\Address */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($modelAddress, 'ulica')->dropDownList(ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica')) ?>

	<div class="row">
    
    <?= $form->field($modelAddress, 'dom', [
    	'options' => ['class' => 'col-md-4', 'style' => 'padding-right: 5px;']
    ]) ?>

    <?= $form->field($modelAddress, 'dom_szczegol', [
    	'options' => ['class' => 'col-md-4', 'style' => 'padding-left: 5px; padding-right: 5px;']    		
    ]) ?>
    
    <?= $form->field($modelAddress, 'lokal', [
    	'options' => ['class' => 'col-md-4', 'style' => 'padding-left: 5px;']
    ]) ?>
	
	</div>
	
    <div class="form-group">
        <?= Html::submitButton($modelAddress->isNewRecord ? 'Dodaj' : 'Edytuj', ['class' => $modelAddress->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
