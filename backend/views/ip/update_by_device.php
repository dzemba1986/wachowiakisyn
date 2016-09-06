<?php 
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\Vlan;
use backend\models\Subnet;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="col-md-9">


<!-- The template for adding new field network -->

<div id="network-template" class="network-group" style="display : none">
	<div class="col-xs-2 form-group" style="padding-left: 0px; padding-right: 3px;">
	
		<?= Html::label('Vlan') ?>
				
		<?= Html::dropDownList('vlan', '', ArrayHelper::map(Vlan::find()->select('id')->orderBy('id')->all(), 'id', 'id'), [
			'class' => 'form-control', 
			'prompt' => 'Vlan',
			'onchange' => new yii\web\JsExpression("
				var row  = $(this).parents('.network-group');
				var index = row.attr('data-network-index');
				
				$.get('" . Url::toRoute('subnet/select-list') . "&vlan=' + $(this).val(), function(data){
					$('select[name=\"network[' + index + '][subnet]\"]').html(data).trigger('change');
				});	
			")  	
		]) ?>
				
	</div>
			
	<div class="col-md-4 form-group" style="padding-left: 3px; padding-right: 3px;">
		
		<?= Html::label('Podsieć') ?>
		
		<?= Html::dropDownList('subnet', '', [], [
			'class' => 'form-control',
// 			'style' => 'padding-left: 3px; padding-right: 3px;'
			'onchange' => new yii\web\JsExpression("
				var row = $(this).parents('.network-group');
				var index = row.attr('data-network-index');

				$.get('" . Url::toRoute('ip/select-list') . "&subnet=' + $(this).val() + '&mode=free', function(data){
					$('select[name=\"network[' + index + '][ip]\"]').html(data);
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
			
	<div class="col-sm-1 form-group">
		<?= Html::label('Usuń') ?>
       	<button type="button" class="btn btn-default remove-button"><i class="glyphicon glyphicon-minus"></i></button>
    </div>					
</div>

<!-- End template -->

<?php 
$key = 0;
$ips = [];
$form = ActiveForm::begin([
		'id' => 'ip',
]);
?>

	<?php if(!empty($modelIps)) : ?>
	
	
	<?php foreach ($modelIps as $key => $modelIp) : 
	
		$ips[] = $modelIp->ip;
	?>
	
		<div class="network-group" data-network-index="<?= $key; ?>" style="display: flex">
			<div class="col-sm-2 form-group" style="padding-left: 0px; padding-right: 3px;">
				
				<?= $key == 0 ? Html::label('Vlan') : null ?>
				
				<?= Html::dropDownList('network[' . $key . '][vlan]', $modelIp->modelSubnet->modelVlan->id, ArrayHelper::map(Vlan::find()->select('id')->all(), 'id', 'id'), [
					'class' => 'form-control',
					'prompt' => 'Vlan',
					'onchange' => new yii\web\JsExpression("
						var row = $(this).parents('.network-group');
						var index = row.attr('data-network-index');
							
						$.get('" . Url::toRoute('subnet/select-list') . "&vlan=' + $(this).val(), function(data){
							$('select[name=\"network[' + index + '][subnet]\"]').html(data).trigger('change');
						});	
					")	
				]) ?>
				
			</div>
			
			<div class="col-sm-4 form-group" style="padding-left: 3px; padding-right: 3px;">
				
				<?= $key == 0 ? Html::label('Podsieć') : null ?>
				
				<?= Html::dropDownList('network[' . $key . '][subnet]', $modelIp->subnet, ArrayHelper::map(Subnet::find()->select(['id', 'ip'])->where(['vlan' => $modelIp->modelSubnet->modelVlan->id])->all(), 'id', 'ip'), [
					'class' => 'form-control',
					'prompt' => 'Podsieć',
					'onchange' => new yii\web\JsExpression("
						var row = $(this).parents('.network-group');
						var index = row.attr('data-network-index');
							
						$.get('" . Url::toRoute('ip/select-list') . "&subnet=' + $(this).val() + '&mode=free', function(data){
							$('select[name=\"network[' + index + '][ip]\"]').html(data);
						});	
					")	
				]) ?>
				
			</div>
			
			<div class="col-md-3 form-group" style="padding-left: 3px;">
				
				<?= $key == 0 ? Html::label('Adres IP') : null ?>
				
				<?= Html::dropDownList('network[' . $key . '][ip]', '', [], [
					'class' => 'form-control',
	// 				'style' => 'padding-left: 3px; padding-right: 3px;'
				]) ?>
				
			</div>
			
			
			<div class="col-sm-1 form-group">
				<?= $key == 0 ? Html::label('Usuń') : null ?>
       			<button type="button" class="btn btn-default remove-button"><i class="glyphicon glyphicon-minus"></i></button>
    		</div>					
	    </div>
	<?php endforeach; ?>
	
		<button type="button" class="btn btn-default add-network-button"><i class="glyphicon glyphicon-plus"></i></button>
	
	<?php else : ?>
	
		
			<button type="button" class="btn btn-default add-network-button"><i class="glyphicon glyphicon-plus"></i></button>
  		
	
	<?php endif; ?>

	<?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>	
		
<?php ActiveForm::end() ?>	

</div>

<script>

$(function() {

	var networkIndex = <?= $key; ?>;

	var ips = <?php echo json_encode($ips) ?>;

    $.each(ips, function(key, value) {
    	$(new Option(value, value)).appendTo("select[name='network[" + key + "][ip]']");
    });	
    

    $('#ip')

        // Add button click handler
        .on('click', '.add-network-button', function() {
            networkIndex++;
            var $template = $('#network-template'),
                $clone    = $template
                                .clone()
                                .css('display', 'flex')
                                .removeAttr('id')
                                .attr('data-network-index', networkIndex)
                                .insertBefore('.add-network-button');

            if($('#ip').children('.network-group').length > 1){

				$clone.children().children('label').remove();
            }
            // Update the name attributes
            $clone
                .find('[name="vlan"]').attr('name', 'network[' + networkIndex + '][vlan]').end()
                .find('[name="subnet"]').attr('name', 'network[' + networkIndex + '][subnet]').end()
                .find('[name="ip"]').attr('name', 'network[' + networkIndex + '][ip]').end();
        })

        // Remove button click handler
        .on('click', '.remove-button', function() {
            var row  = $(this).parents('.network-group');
                //index = row.attr('data-network-index');
			//console.log(row);
            row.remove();
        })

    	.on('beforeSubmit', function(e){

    		var form = $(this);
	     	$.post(
	      		form.attr("action"), // serialize Yii2 form
	      		form.serialize()
	     	).done(function(result){
    		
//     			console.log(result);
     			if(result == 1){
     				$("#device_tree").jstree(true).refresh();
//     				$('#modal-update-net').modal('hide');
//      			$.pjax.reload({container: '#subnet-grid-pjax'});
     			}
     			else{
    		
     				$('#message').html(result);
     			}
     		}).fail(function(){
     			console.log('server error');
     		});
    		return false;				
    	});
});
</script>