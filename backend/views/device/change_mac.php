<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Device;

$parent = $modelDevice->modelTree[0]->parent_device;	//id parent
$parentPortIndex = $modelDevice->modelTree[0]->parent_port; //index port

$modelDeviceParent = Device::findOne($parent);
$arPortsParent = $modelDeviceParent->modelModel->port;

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


$form = ActiveForm::begin([
	'id' => $modelDevice->formName(),
	//'enableClientValidation'=>true,
])?>
	
	<?= $form->field($modelDevice, 'mac', []) ?>

    <?= Html::submitButton('Zapisz', ['class' => 'btn btn-primary']) ?>
    
    <button class="change btn" type="button">Skrypt</button>
    
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

    	if (regex.test(mac)) {
    	
			var clipboard = new Clipboard(".change", {
				text : function() {
					return 'interface ethernet ' + <?= json_encode($arPortsParent[$parentPortIndex]) ?> + "\n" +
							"shutdown\n" +
							"no port security\n" +
							"exit" +
							"interface vlan " + <?= json_encode($modelDevice->modelIps[0]->modelSubnet->modelVlan->id) ?> + "\n" +
							"no bridge address " + <?= json_encode($modelDevice->mac) ?> + "\n" +
							"bridge address " + $("#host-mac").val() + " permanent ethernet " + <?= json_encode($arPortsParent[$parentPortIndex]) ?> + "\n" +
							"exit\n" +
							"interface ethernet " + <?= json_encode($arPortsParent[$parentPortIndex]) ?> + "\n" +
							"port security mode lock\n" +
							"port security discard\n" +
							"no shutdown\n" +
							"exit\n" +
							"exit\n" +
							"copy r s\n" +		
							"y\n";
				}
			});

			clipboard.on('success', function(e) {
				$('#log').text('Skrypt zmiany MAC w schowku');;
	        });
    	} else {
        	alert("Nieprawidłowy format adresu MAC");
    	}
    	
		
    });
// 	clipboard.on('success', function(e) {
// 		if(e.trigger.textContent == 'Dodaj')
// 			$('#log').text('Skrypt dodaj w schowku');
// 		else if(e.trigger.textContent == 'Usuń') 
// 			$('#log').text('Skrypt usuń w schowku');
// // 		console.log(e.trigger.textContent);
// 		//e.clearSelection();
// 	});


    
});
</script>





