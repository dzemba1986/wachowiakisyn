<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Device;

$parent = $modelDevice->modelTree[0]->parent_device;	//id parent
$parentPortIndex = $modelDevice->modelTree[0]->parent_port; //index port

$modelDeviceParent = Device::findOne($parent);
$arPortsParent = $modelDeviceParent->modelModel->port;

if($modelDeviceParent->modelModel->config == 1){

$change = 'interface ethernet ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
no port security' . '
exit' . '
interface vlan ' . $modelDevice->modelIps[0]->modelSubnet->modelVlan->id . '
no bridge address ' . $modelDevice->mac . '
bridge address ' . $modelDevice->mac . ' permanent ethernet ' . $arPortsParent[$parentPortIndex] . '
exit' . '
interface ethernet ' . $arPortsParent[$parentPortIndex] . '
port security mode lock' . '
port security discard' . '
no shutdown' . '
exit' . '
exit' . '
copy r s' . '		
y' . '
';

} elseif($modelDeviceParent->modelModel->config == 2){

$change = 'interface ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
no switchport port-security' . '
exit' . '
no mac address-table static ' . preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac)) . ' forward interface ' . $arPortsParent[$parentPortIndex] . ' vlan ' . $modelDevice->modelIps[0]->modelSubnet->modelVlan->id . '
no mac address-table static ' . preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac)) . ' forward interface ' . $arPortsParent[$parentPortIndex] . ' vlan ' . $modelDevice->modelIps[0]->modelSubnet->modelVlan->id . '
exit' . '
clear ip dhcp snooping binding ' . $modelDevice->modelIps[0]->ip . '
configure terminal' . '
interface ' . $arPortsParent[$parentPortIndex] . '
no shutdown' . '
exit' . '
exit' . '
wr' . '
';
	
}

$form = ActiveForm::begin([
	'id' => $modelDevice->formName(),
	//'enableClientValidation'=>true,
])?>
	
	<?= $form->field($modelDevice, 'mac', []) ?>

    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary save', 'disabled' => true]) ?>
    
    <button class="change btn" type="button" data-clipboard-text="<?= $change; ?>">Skrypt</button>
    
    <p id="log"></p>
    
<?php ActiveForm::end() ?>

<script>

$(function() {

	
	
    $('#<?= $modelDevice->formName(); ?>').on('beforeSubmit', function(e){

    	var form = $(this);
     	$.post(
      		form.attr("action"), // serialize Yii2 form
      		form.serialize()
     	).done(function(result){
    		
//     		console.log(result);
     		if(result == 1){
//      			$("#device_tree").jstree(true).refresh();
    			$('#modal-change-mac').modal('hide');
    			$("#device_desc").load(location.href + " #device_desc");
//      		$.pjax.reload({container: '#device-desc-pjax'});
     		}
     		else{
    		
     			$('#message').html(result);
     		}
     	}).fail(function(){
     		console.log('server error');
     	});
    	return false;				
    });

    $('.change').click(function(){
        
    	var mac = $("#host-mac").val();

    	var regex = /^(([a-fA-F0-9]{2}-){5}[a-fA-F0-9]{2}|([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}|([0-9A-Fa-f]{4}\.){2}[0-9A-Fa-f]{4})?$/;

    	//odblokuj button "zapisz"
    	$(".save").attr("disabled", false);

    	if (regex.test(mac)) {
    	
			var clipboard = new Clipboard(".change");

			clipboard.on('success', function(e) {
				$('#log').text('Skrypt zmiany MAC w schowku');;
	        });
    	} else {
        	alert("Nieprawidłowy format adresu MAC");
    	}
    	
		
    });
});
</script>





