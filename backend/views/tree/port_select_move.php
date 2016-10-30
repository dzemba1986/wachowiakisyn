<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?= Html::beginForm(
		Url::toRoute('tree/move'),
		'post',
		['id' => 'port-select']
		);
?>

<?= Html::dropDownList('parentPort', 1, [], ['calss' => 'form-control']); ?>

<?= Html::submitButton('Wybierz', ['class' => 'btn btn-primary']); ?>

<?= Html::endForm(); ?>

<script>

var device = $('#modal-port-select').data('device');
var port = $('#modal-port-select').data('port');
var newParentDevice = $('#modal-port-select').data('newParentDevice');
var mode = $('#modal-port-select').data('mode');


$.get( " <?= Url::toRoute('tree/select-list-port') ?>&device=" + newParentDevice, function(data){
	$("select[name='parentPort']").html(data);
} );

$( '#port-select' ).submit(function( event ) {

	var tree = $("#device_tree").jstree(true);
	var newParentPort = $('select[name="parentPort"]').val();

	$.post(
		$(this).attr("action"),
	  	{
	  		device : device,
	  		port : 	port,
			newParentDevice : newParentDevice,
			newParentPort : newParentPort				
       	}
	).done(function(result){
			
		if(result == 1){
			$(this).trigger('reset');
			$('#modal-port-select').modal('hide');
	 		tree.refresh();
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