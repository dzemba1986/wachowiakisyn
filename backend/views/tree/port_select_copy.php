<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?= Html::beginForm(
		Url::toRoute('tree/copy'),
		'post',
		['id' => 'port-select']
		);
?>

<?= Html::dropDownList('localPort', 1, [], ['calss' => 'form-control']); ?>

<?= Html::dropDownList('parentPort', 1, [], ['calss' => 'form-control']); ?>

<?= Html::submitButton('Wybierz', ['class' => 'btn btn-primary']); ?>

<?= Html::endForm(); ?>

<script>

var device = $('#modal-port-select').data('device');
var newParentDevice = $('#modal-port-select').data('newParentDevice');

$.get( " <?= Url::toRoute('tree/free-port-list') ?>&id=" + device, function(data){
	$("select[name='localPort']").html(data);
} );

$.get( " <?= Url::toRoute('tree/free-port-list') ?>&id=" + newParentDevice, function(data){
	$("select[name='parentPort']").html(data);
} );

$( '#port-select' ).submit(function( event ) {

	var tree = $("#device_tree").jstree(true);
	var newParentPort = $('select[name="parentPort"]').val();
	var port = $('select[name="localPort"]').val()

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