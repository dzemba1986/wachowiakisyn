<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Address;


/**
 * @var yii\web\View $this
 * @var app\models\Modyfication $modelTask
 * @var yii\widgets\ActiveForm $form
 */
$modelAddress = new Address();
?>

<div style="display: inline-block; width:29%;">	
<?= $form->field($modelAddress, 'ulica')->dropDownList(ArrayHelper::map(Address::find()->select('ulica')->groupBy('ulica')->all(), 'ulica', 'ulica')) ?>
</div>
    
<div style="display: inline-block; width:10%; padding: 0 0 0 2%">	
    <?= $form->field($modelAddress, 'dom')->textInput() ?>
</div>

<div style="display: inline-block; width:10%; padding: 0 0 0 2%">	
    <?= $form->field($modelAddress, 'dom_szczegol')->textInput() ?>
</div>

<div style="display: inline-block; width:10%; padding: 0 0 0 2%">	
    <?= $form->field($modelAddress, 'lokal')->textInput() ?>
</div>	

<div style="display: inline-block; width:25%; padding: 0 0 0 2%">	
    <?= $form->field($modelAddress, 'lokal_szczegol')->label('Nazwa')->textInput() ?>
</div>