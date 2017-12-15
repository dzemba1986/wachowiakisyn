<?php

use backend\models\Device;
$parent = $modelDevice->modelTree[0]->parent_device;	//id parent
$parentPortIndex = $modelDevice->modelTree[0]->parent_port; //index port
$portNumber = $parentPortIndex + 1;

$modelDeviceParent = Device::findOne($parent);
$arPortsParent = $modelDeviceParent->modelModel->port;

if($modelDeviceParent->modelModel->config == 1){
    
$add = <<<ADD
interface vlan 3
bridge address {$modelDevice->mac} permanent ethernet {$arPortsParent[$parentPortIndex]}
ip access-list cam{$portNumber}
deny-udp any any any 68
permit any {$modelIps[0]->ip} 0.0.0.0 213.5.208.128 0.0.0.63
permit any {$modelIps[0]->ip} 0.0.0.0 192.168.5.0 0.0.0.255
permit-udp 0.0.0.0 0.0.0.0 68 any 67
exit
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->modelAddress->toString(true)}
switchport access vlan 3
service-acl input cam{$portNumber}
port security mode lock
port security discard
no shutdown
exit
exit
cop r s
y

ADD;
    
$delete = <<<DELETE
interface vlan 3
no bridge address {$modelDevice->mac}
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
no description
switchport access vlan 555
no service-acl input
no port security
no shutdown
exit
no ip access-list cam{$portNumber}
exit
cop r s 
y

DELETE;
    
} elseif($modelDeviceParent->modelModel->config == 2){
    
    $preg_replace = function($pattern, $replacement, $subject) {
        return preg_replace($pattern, $replacement, $subject);
    };
            
$add = <<<ADD
access-list hardware voip{$portNumber}
deny udp any any eq 68
permit ip {$modelIps[0]->ip} 0.0.0.0 213.5.208.128 0.0.0.63
permit ip {$modelIps[0]->ip} 0.0.0.0 192.168.5.0 0.0.0.255
permit udp 0.0.0.0 0.0.0.0 eq 68 any eq 67
deny ip any any
exit
interface {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->modelAddress->toString(true)}
switchport access vlan {$modelIps[0]->modelSubnet->modelVlan->id}
access-group voip{$portNumber}
no shutdown
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
exit
mac address-table static {$preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac))} forward interface {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
exit
wr

ADD;
        
$delete = <<<DELETE
no mac address-table static {$preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac))} forward interface {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
interface {$arPortsParent[$parentPortIndex]}
shutdown
no access-group voip{$portNumber}
no description
no switchport port-security
switchport access vlan 555
no shutdown
exit
no access-list hardware voip{$portNumber}
exit
wr

DELETE;
        
} 
    ?>
<?php if(isset($modelDeviceParent->modelIps[0])) : ?>
<a href="ssh://<?= $modelDeviceParent->modelIps[0]->ip; ?>:22222">Zaloguj</a>
<?php endif; ?>

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