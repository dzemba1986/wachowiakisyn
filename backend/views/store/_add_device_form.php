<?php 

use backend\models\DeviceType;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

require_once '_modal_store.php';

$form = ActiveForm::begin([
    'action' => ['store/add'],
    'id' => 'add-store-form'
]); ?>

<div class="row">

	<?= $form->field($searchModel, 'type_id', [
		'options' => ['class' => 'col-md-2', 'style' => 'padding-right: 5px;'],
		'template' => "{input}\n{hint}\n{error}"
	])->dropDownList(ArrayHelper::map(DeviceType::findOrderName()->all(), 'id', 'name'),['prompt' => 'Wybierz typ',])?>

	<?= Html::submitButton('Dodaj', ['class' => 'btn btn-success add-store col-md-1']) ?>

</div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
$(function() {

    $('body').on('click', '.add-store', function(event){
    
		if( !$("select[name='DeviceSearch[type_id]']").val() )
			alert("Nie wybrano typu urządzenia!");
		else {
		  $('#modal-store').modal('show')
			.find('#modal-content-store')
			.load($("#add-store-form").attr('action') + '&typeId=' + $("select[name='DeviceSearch[type_id]']").val());
    	}
    	
        return false;
	});
});
JS;
$this->registerJs($js);
?>