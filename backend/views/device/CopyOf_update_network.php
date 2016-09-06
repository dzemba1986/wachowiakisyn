<?php 
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\Vlan;
use backend\models\Subnet;
use yii\helpers\Url;

if(!empty($modelIps)) :

	foreach ($modelIps as $key => $modelIp) : ?>
	
		<div class="row form-group" data-network-index="<?php echo $key; ?>">
			<div class="col-sm-2" style="padding-left: 3px; padding-right: 3px;">
				
				<?= $key == 0 ? Html::label('Vlan') : null ?>
				
				<?= Html::dropDownList('network[' . $key . '][vlan]', $modelIp->modelSubnet->modelVlan->id, ArrayHelper::map(Vlan::find()->select('id')->all(), 'id', 'id'), [
					'class' => 'form-control',
					'prompt' => 'Vlan',
					'onchange' => new yii\web\JsExpression("
						var row = $(this).parents('.form-group');
						var index = row.attr('data-network-index');
							
						$.get('" . Url::toRoute('subnet/list') . "&vlan=' + $(this).val(), function(data){
							$('select[name=\"network[' + index + '][subnet]\"]').html(data).trigger('change');
						});	
					")	
				]) ?>
				
			</div>
			
			<div class="col-sm-4" style="padding-left: 3px; padding-right: 3px;">
				
				<?= $key == 0 ? Html::label('Podsieć') : null ?>
				
				<?= Html::dropDownList('network[' . $key . '][subnet]', $modelIp->subnet, ArrayHelper::map(Subnet::find()->select(['id', 'ip'])->where(['vlan' => $modelIp->modelSubnet->modelVlan->id])->all(), 'id', 'ip'), [
					'class' => 'form-control',
					'prompt' => 'Podsieć',
					'onchange' => new yii\web\JsExpression("
						var row = $(this).parents('.form-group');
						var index = row.attr('data-network-index');
							
						$.get('" . Url::toRoute('ip/select-list') . "&subnet=' + $(this).val() + '&mode=free', function(data){
							$('select[name=\"network[' + index + '][ip]\"]').html(data);
						});	
					")	
				]) ?>
				
			</div>
			
			<div class="col-sm-4" style="padding-left: 3px;">
				
				<?= $key == 0 ? Html::label('Adres IP') : null ?>
				
				<?= Html::dropDownList('network[' . $key . '][ip]', '', [], [
					'class' => 'form-control',
	// 				'style' => 'padding-left: 3px; padding-right: 3px;'
				]) ?>
				
			</div>
			
			<?php if($key == 0) : ?>
				<div class="col-sm-1">
	            	<button type="button" class="btn btn-default add-button" style="margin-top: 25px;"><i class="glyphicon glyphicon-plus"></i></button>
	        	</div>
        	<?php else : ?>
        		<div class="col-sm-1">
            		<button type="button" class="btn btn-default remove-button"><i class="glyphicon glyphicon-minus"></i></button>
        		</div>
        	<?php endif; ?>					
	    </div>
	<?php endforeach; ?>
<?php else :?>

	<div class="col-sm-1">
		<button type="button" class="btn btn-default add-network-button" style="margin-top: 25px;"><i class="glyphicon glyphicon-plus"></i></button>
  	</div>

<?php endif; ?>	
	    
	    <!-- The template for adding new field network -->
	    <div id="network-template" class="row form-group" style="display : none">
			<div class="col-sm-2" style="padding-left: 3px; padding-right: 3px;">
				
				<?= Html::dropDownList('vlan', '', ArrayHelper::map(Vlan::find()->select('id')->orderBy('id')->all(), 'id', 'id'), [
					'class' => 'form-control', 
					'prompt' => 'Vlan',
					'onchange' => new yii\web\JsExpression("
						var row  = $(this).parents('.form-group');
						var index = row.attr('data-network-index');
						
						$.get('" . Url::toRoute('subnet/list') . "&vlan=' + $(this).val(), function(data){
							$('select[name=\"network[' + index + '][subnet]\"]').html(data).trigger('change');
						});	
					")  	
				]) ?>
				
			</div>
			
			<div class="col-sm-4" style="padding-left: 3px; padding-right: 3px;">
				
				<?= Html::dropDownList('subnet', '', [], [
					'class' => 'form-control',
	// 				'style' => 'padding-left: 3px; padding-right: 3px;'
					'onchange' => new yii\web\JsExpression("
						var row = $(this).parents('.form-group');
						var index = row.attr('data-network-index');
				
						$.get('" . Url::toRoute('ip/select-list') . "&subnet=' + $(this).val() + '&mode=free', function(data){
							$('select[name=\"network[' + index + '][ip]\"]').html(data);
						});
					")
				]) ?>
				
			</div>
			
			<div class="col-sm-4" style="padding-left: 3px;">
				
				<?= Html::dropDownList('ip', '', [], [
					'class' => 'form-control',
	// 				'style' => 'padding-left: 3px; padding-right: 3px;'
				]) ?>
				
			</div>
			
			<div class="col-sm-1">
            	<button type="button" class="btn btn-default remove-button"><i class="glyphicon glyphicon-minus"></i></button>
        	</div>
        					
	    	
	    </div>
		<!-- End template -->
		
		<?php 
		
		foreach ($modelIps as $modelIp){
			$ips[] = $modelIp->ip;
		}
		?>
	
<script>

$(function() {

    var networkIndex = <?php echo $key; ?>;
    var ips = <?php echo json_encode($ips) ?>;

    $.each(ips, function(key, value) {
    	$(new Option(value, value)).appendTo("select[name='network[" + key + "][ip]']");
    });	
    

    $('#<?= $modelDevice->formName(); ?>')

        // Add button click handler
        .on('click', '.add-network-button', function() {
            networkIndex++;
            var $template = $('#network-template'),
                $clone    = $template
                                .clone()
                                .removeAttr('style')
                                .removeAttr('id')
                                .attr('data-network-index', networkIndex)
                                .insertBefore($template);

            // Update the name attributes
            $clone
                .find('[name="vlan"]').attr('name', 'network[' + networkIndex + '][vlan]').end()
                .find('[name="subnet"]').attr('name', 'network[' + networkIndex + '][subnet]').end()
                .find('[name="ip"]').attr('name', 'network[' + networkIndex + '][ip]').end();
        })

        // Remove button click handler
        .on('click', '.remove-button', function() {
            var $row  = $(this).parents('.form-group'),
                index = $row.attr('data-network-index');

            $row.remove();
        });
});
</script>