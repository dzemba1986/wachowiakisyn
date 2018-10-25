<?php

use kartik\growl\GrowlAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */

GrowlAsset::register($this);

$form = ActiveForm::begin([
	'id' => 'to-store'
]) ?>
    <p>Operacja przeniesie urządzenie do magazynu i zwolni wszystkie adresy IP powiązane z urządzeniem. Czy na pewno?</p>
    
    <?= Html::submitButton('Tak', ['class' => 'btn btn-success']) ?>
  
    <?= Html::button('Nie', ['class' => 'no btn btn-danger']) ?>

<?php ActiveForm::end() ?>

<?php
$urlView = Url::to(['tabs-view']);

$js = <<<JS
$(function(){

    $('.modal-header h4').html('Przenieś do magazynu');
        
    $('.no').click(function() {
        var tree = $("#device_tree").jstree(true);

		$('#modal-sm').modal('hide');
        tree.refresh();
    });

    $('#to-store').on('beforeSubmit', function(e){
		
		var form = $(this);
	 	$.post(
	  		form.attr('action'),
	  		form.serialize()
	 	).done(function(result){
	 		if (result == 1) {
				$('#modal-sm').modal('hide');
                $.notify('Usunięto urządzenie.', {
                    type: 'success',
                    placement : {from : 'top', align : 'right'},
                });
	 		}
	 		else {
                $('#modal-sm').modal('hide');
                var tree = $("#device_tree").jstree(true);
                tree.refresh();
                //$('#device_desc').load('{$urlView}&id=' + $('#device-select').val());
                $.notify('Błąd usuwania urządzenia (' + result + ').', {
                    type: 'danger',
                    placement : {from : 'top', align : 'right'},
                });
	 		}
	 	}).fail(function(){
	 		console.log('server error');
	 	});
		return false;				
	});	
});
JS;

$this->registerJs($js);
?>