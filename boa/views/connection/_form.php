<?php
/* @var $this View */
/* @var $modelConnection Connection */
/* @var $form ActiveForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use yii\widgets\MaskedInput;
?>

<div style="float:left; width:90%">
<?php $form = ActiveForm::begin([
	'id' => 'update-connection-form',
])?>
	<div style="float:left; width:50%">
	<div style="float:left; width:40%">
	<?= $form->field($modelConnection, 'ara_id')->widget(MaskedInput::className(), [
													'mask' => 'x9{1,4}',
													'definitions' => [
														'x' => [
															'validator' => "[0-9aA]",
															'cardinality' => 1,
															'casing' => "lower",
														]
													]
											]) ?>
	<?= $form->field($modelConnection, 'phone')->widget(MaskedInput::className(), [
													'mask' => '(9{1,2})-9{1,2}-9{1,2}-9{1,3}'					
											]) ?>
	<?= $form->field($modelConnection, 'phone2')->widget(MaskedInput::className(), [
													'mask' => '9{1,3}-9{1,3}-9{1,3}'					
											]) ?>
	<?= $form->field($modelConnection, 'mac')->widget(MaskedInput::className(), [
													'mask' => '[hh:hh:hh:hh:hh:hh]',
													'clientOptions' => [
															//'onincomplete' => 'function(){ alert("inputmask incomplete"); }',
															//'showMaskOnHover' => false,
															//'showMaskOnFocus' => false
														],
													'definitions' => [
														'h' => [
															'validator' => "[0-9a-fA-F]",
															'cardinality' => 1,
															'casing' => "lower",
														]
													]					
											]) ?>
	<?= $form->field($modelConnection, 'port')->dropDownList([1,2,3]) ?>
	</div>
	
	<div style="float:right; width:40%">
	<?= $form->field($modelConnection, 'start_date')->widget(DatePicker::className(), [
							'model' => $modelConnection,
    						'attribute' => 'start_date',
        					'language'=>'pl',	
    						//'template' => '{addon}{input}',
        					'clientOptions' => [
            					'autoclose' => true,
            					'format' => 'yyyy-mm-dd'
        					]		
						])?>
					
					
	<?= $form->field($modelConnection, 'conf_date')->widget(DatePicker::className(), [
							'model' => $modelConnection,
    						'attribute' => 'conf_date',
        					'language'=>'pl',	
    						//'template' => '{addon}{input}',
        					'clientOptions' => [
            					'autoclose' => true,
            					'format' => 'yyyy-mm-dd'
        					]
						])?>	
								
					
	<?= $form->field($modelConnection, 'activ_date')->widget(DatePicker::className(), [
							'model' => $modelConnection,
    						'attribute' => 'activ_date',
        					'language'=>'pl',	
    						//'template' => '{addon}{input}',
        					'clientOptions' => [
            					'autoclose' => true,
            					'format' => 'yyyy-mm-dd'
        					]
						])?>
					
	
	<?= $form->field($modelConnection, 'pay_date')->widget(DatePicker::className(), [
							'model' => $modelConnection,
    						'attribute' => 'pay_date',
        					'language'=>'pl',	
    						//'template' => '{addon}{input}',
        					'clientOptions' => [
            					'autoclose' => true,
            					'format' => 'yyyy-mm-dd'
        					]
							//'clientOptions'=>['gotoCurrent'=>true]
						])?>
					
						
	<?= $form->field($modelConnection, 'resignation_date', ['options' => ['style' => 'color : red']])->widget(DatePicker::className(), [
							'model' => $modelConnection,
    						'attribute' => 'resignation_date',
        					'language'=>'pl',	
    						//'template' => '{addon}{input}',
        					'clientOptions' => [
            					'autoclose' => true,
            					'format' => 'yyyy-mm-dd'
        					]
							//'clientOptions'=>['gotoCurrent'=>true]
						])?>					
	</div>
	</div>
	
	
	<div style="width:50%">
	<?= $form->field($modelConnection, 'info')->textarea(['style' => 'resize: vertical']) ?>
	<?= $form->field($modelConnection, 'info_boa')->textarea(['style' => 'resize: vertical']) ?>
	<?= Html::submitButton($modelConnection->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => 'btn btn-primary']) ?>
	</div>
<?php ActiveForm::end() ?>
</div>	
