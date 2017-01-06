<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\Vlan;
use backend\models\Subnet;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


$form = ActiveForm::begin([
		'id' => 'add-host',
]);
?>
		<div class="network-group" style="display: flex">
			<div class="col-sm-2 form-group" style="padding-left: 0px; padding-right: 3px;">
				
				<?= Html::label('Vlan') ?>
				
				<?= Html::dropDownList('vlan', '', ArrayHelper::map(Vlan::find()->select('id')->orderBy('id')->all(), 'id', 'id'), [
					'class' => 'form-control', 
					'prompt' => 'Vlan',
					'onchange' => new yii\web\JsExpression("
						var row  = $(this).parents('.network-group');
						var index = row.attr('data-network-index');
						
						$.get('" . Url::toRoute('subnet/select-list') . "&vlan=' + $(this).val(), function(data){
							$('select[name=\"subnet\"]').html(data).trigger('change');
						});	
					")  	
				]) ?>
				
			</div>
			
			<div class="col-sm-5 form-group" style="padding-left: 3px; padding-right: 3px;">
				
				<?= Html::label('Podsieć')?>
				
				<?= Html::dropDownList('subnet', '', [], [
					'class' => 'form-control',
		// 			'style' => 'padding-left: 3px; padding-right: 3px;'
					'onchange' => new yii\web\JsExpression("
						var row = $(this).parents('.network-group');
						var index = row.attr('data-network-index');
		
						$.get('" . Url::toRoute('ip/select-list') . "&subnet=' + $(this).val() + '&mode=free', function(data){
							$('select[name=\"ip\"]').html(data);
						});
					")
				]) ?>
				
			</div>
			
			<div class="col-md-3 form-group" style="padding-left: 3px;">
				
				<?= Html::label('Adres IP') ?>
				
				<?= Html::dropDownList('ip', '', [], [
					'class' => 'form-control',
		// 			'style' => 'padding-left: 3px; padding-right: 3px;'
				]) ?>
				
			</div>
			
			<?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>	
	    </div>

	
		
<?php ActiveForm::end() ?>	

<script>

$(function(){
	$("#add-host").on('beforeSubmit', function(e){

		var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
		
// 			console.log(result);
 			if(result == 1){
 				$("#device_tree").jstree(true).refresh();
// 				$('#modal-update-net').modal('hide');
//  			$.pjax.reload({container: '#subnet-grid-pjax'});
 			}
 			else{
 				alert(result);
 			}
 		}).fail(function(){
 			console.log('server error');
 		});
		return false;				
	});
});

</script>
