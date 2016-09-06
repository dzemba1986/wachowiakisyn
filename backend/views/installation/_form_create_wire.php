<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\models\Device;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Installation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="create-installation-form">

	<?php  echo '<center><h4>'.$modelConnection->modelAddress->fullAddress.'</h4></center>'; ?>

    <?php $form = ActiveForm::begin(['id'=>$modelInstallation->formName()]); ?>
    
    <?= $form->field($modelConnection, 'device')->widget(Select2::classname(), [
                                        'data' => $arDevice,
                                        'language' => 'pl',
                                        'options' => ['placeholder' => 'Wybierz urządzenie'],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
    ])->label('Urządzenie'); ?>
    
    <?= $form->field($modelConnection, 'port')->dropDownList(array_slice(range(0,47), 1, null, true)) ?>
    
    <?= $form->field($modelInstallation, 'wire_date')->textInput()->widget(DatePicker::className(), [
    				'model' => $modelInstallation,
    				'attribute' => 'wire_date',
        			'language'=>'pl',	
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'endDate' => '0d', //wybór daty max do dziś
                    ]
				]) ?>

    <?= $form->field($modelInstallation, 'wire_length')->textInput(['maxlength' => true])->label('Kabel') ?>

    <?= $form->field($modelInstallation, 'wire_user')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($modelInstallation->isNewRecord ? 'Dodaj' : 'Update', ['class' => $modelInstallation->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>

$('#<?= $modelInstallation->formName(); ?>').on('beforeSubmit', function(e){

	var form = $(this);
 	$.post(
  		form.attr("action"), // serialize Yii2 form
  		form.serialize()
 	).done(function(result){
		
// 		console.log(result);
 		if(result == 1){
 			$(form).trigger('reset');
			$('#modal_create_installation').modal('hide');
 			$.pjax.reload({container: '#connection-grid-pjax'});
 		}
 		else{
		
 			$('#message').html(result);
 		}
 	}).fail(function(){
 		console.log('server error');
 	});
	return false;				
});

</script>