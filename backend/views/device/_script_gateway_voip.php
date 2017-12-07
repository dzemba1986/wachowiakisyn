<?php 

use backend\models\Device;

$parent = $modelDevice->modelTree[0]->parent_device;	//id parent
$parentPortIndex = $modelDevice->modelTree[0]->parent_port; //index port
$portNumber = $parentPortIndex + 1;

$modelDeviceParent = Device::findOne($parent);
$arPortsParent = $modelDeviceParent->modelModel->port;

if($modelDeviceParent->modelModel->config == 1){

$add = <<<ADD
ip access-list voip {$portNumber}
deny-udp any any any 68
permit any {$modelDevice->modelIps[0]->ip} 0.0.0.0 213.5.208.0 0.0.0.63
permit any {$modelDevice->modelIps[0]->ip} 0.0.0.0 213.5.208.128 0.0.0.63
permit any {$modelDevice->modelIps[0]->ip} 0.0.0.0 10.111.0.0 0.0.255.255
permit-udp 0.0.0.0 0.0.0.0 68 any 67
exit
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
switchport trunk allowed vlan remove all
switchport mode access
description {$modelDevice->name}
switchport access vlan {$modelDevice->modelIps[0]->modelSubnet->modelVlan->id}
spanning-tree portfast
spanning-tree bpduguard
service-acl input voip{$portNumber}
no shutdown
exit
exit
copy r s
y

ADD;

$delete = <<<DELETE
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
switchport access vlan 555
no service-acl input
no shutdown
exit
no ip access-list voip{$portNumber}
no ip access-list voip{$portNumber}
exit
copy r s
y

DELETE;
}
elseif($modelDeviceParent->modelModel->config == 2){

$add = <<<ADD
access-list hardware voip{$portNumber}
deny udp any any eq 68
permit ip {$modelDevice->modelIps[0]->ip} 0.0.0.0 213.5.208.0 0.0.0.63
permit ip {$modelDevice->modelIps[0]->ip} 0.0.0.0 213.5.208.128 0.0.0.63
permit ip {$modelDevice->modelIps[0]->ip} 0.0.0.0 10.111.0.0 0.0.255.255
permit udp 0.0.0.0 0.0.0.0 eq 68 any eq 67
deny ip any any
exit
interface {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->name}
switchport access vlan {$modelDevice->modelIps[0]->modelSubnet->modelVlan->id}
access-group voip{$portNumber}
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
no shutdown
exit
exit
wr

ADD;

$delete = <<<DELETE
interface {$arPortsParent[$parentPortIndex]}
shutdown
no access-group voip{$portNumber}
switchport access vlan 555
no shutdown
exit
no access-list hardware voip{$portNumber}
exit
wr

DELETE;
}
?>

<button class="btn" data-clipboard-text="<?= $add; ?>">Dodaj</button>
<button class="btn" data-clipboard-text="<?= $delete; ?>">Usuń</button>
<p id="log"></p>

<script>

$(function(){
	var clipboard = new Clipboard('.btn');
	
	clipboard.on('success', function(e) {
		if(e.trigger.textContent == 'Dodaj')
			$('#log').text('Skrypt dodaj w schowku');
		else if(e.trigger.textContent == 'Usuń') 
			$('#log').text('Skrypt usuń w schowku');
// 		console.log(e.trigger.textContent);
		//e.clearSelection();
	});
});

</script>