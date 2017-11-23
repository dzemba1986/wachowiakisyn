<?php 

use backend\models\Device;


//vlan hosta
//mac hosta
//port hosta
//name hosta
//

//print_r($arHosts); exit();


// var_dump($arPortsParent);

$add = '';
$delete = '';

if($modelDevice->modelModel->config == 1){

foreach ($arHosts as $modelHost){
	
$parentPortIndex = $modelHost->modelTree[0]->parent_port; //index port


$arPortsParent = $modelDevice->modelModel->port;

if($modelHost->type == 5){
$add .= 'interface ethernet ' . $arPortsParent[$parentPortIndex] . '
no service-acl input' . '
exit' . '
no ip access-list user' . ($parentPortIndex + 1) . '
no ip access-list user' . ($parentPortIndex + 1) . '
interface vlan ' . $modelHost->modelIps[0]->modelSubnet->modelVlan->id . '
bridge address ' . $modelHost->mac . ' permanent ethernet ' . $arPortsParent[$parentPortIndex] . '
exit' . '
ip access-list user' . ($parentPortIndex + 1) . '
deny-udp any any any 68' . '
deny-tcp any any any 25' . '
permit any ' . $modelHost->modelIps[0]->ip . ' 0.0.0.0 any' . '
permit-udp 0.0.0.0 0.0.0.0 68 any 67' . '
exit' . '
interface ethernet ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
switchport trunk allowed vlan remove all' . '
switchport mode access' . '
switchport access vlan ' . $modelHost->modelIps[0]->modelSubnet->modelVlan->id . '
description ' . $modelHost->modelAddress->toString(true) . '
service-acl input user' . ($parentPortIndex + 1) . '
traffic-shape 520000 5200000' . '
rate-limit 800000' . '
port security mode lock' . '
port security discard' . '
spanning-tree portfast' . '
spanning-tree bpduguard' . '
no shutdown' . '
exit' . '
';
} else {
$add .= 'ip access-list voip' . ($parentPortIndex + 1) . '
deny-udp any any any 68' . '
permit any ' . $modelHost->modelIps[0]->ip . ' 0.0.0.0 213.5.208.0 0.0.0.63' . '
permit any ' . $modelHost->modelIps[0]->ip . ' 0.0.0.0 213.5.208.128 0.0.0.63' . '
permit any ' . $modelHost->modelIps[0]->ip . ' 0.0.0.0 10.111.0.0 0.0.255.255' . '
permit-udp 0.0.0.0 0.0.0.0 68 any 67' . '
exit' . '
interface ethernet ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
switchport trunk allowed vlan remove all' . '
switchport mode access' . '
description ' . 'voip' . $modelHost->name . '
switchport access vlan ' . $modelHost->modelIps[0]->modelSubnet->modelVlan->id . '
spanning-tree portfast' . '
spanning-tree bpduguard' . '
service-acl input voip' . ($parentPortIndex + 1) . '
no shutdown' . '
exit' . '
';
}	
	
$delete .= 'interface vlan ' . $modelHost->modelIps[0]->modelSubnet->modelVlan->id . '
no bridge address ' . $modelHost->mac . '
exit' . '
! Podac port klienta' . '
interface ethernet ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
no service-acl input' . '
no traffic-shape' . '
no rate-limit' . '
no port security' . '
sw a v 555' . '
no shutdown' . '
exit' . '
no ip access-list user' . ($parentPortIndex + 1) . '
no ip access-list user' . ($parentPortIndex + 1) . '
exit' . '
copy r s' . '
y' . '
';
}
$add .= 'exit' . '
copy r s' . '
y' . '
';
}
elseif($modelDevice->modelModel->config == 2){

$i = 1;	
foreach ($arHosts as $modelHost){
		
$parentPortIndex = $modelHost->modelTree[0]->parent_port; //index port
		
$arPortsParent = $modelDevice->modelModel->port;

if ($i == 1) {
$add .= 'do clear ip dhcp snooping binding vlan 4' . '
';
}

$add .= 'interface ' . $arPortsParent[$parentPortIndex]. '		
shutdown' . '			
no shutdown' . '
exit' . '		
';		

$i++;
// $delete = 'interface ' . $arPortsParent[$parentPortIndex] . '
// shutdown' . '
// no access-group voip' . ($parentPortIndex + 1) . '		
// switchport access vlan 555' . '
// no shutdown' . '		
// exit' . '		
// no access-list hardware voip' . ($parentPortIndex + 1) . '		
// exit' . '
// wr' . '
// ';
}
}
?>

<button class="btn" data-clipboard-text="<?= $add; ?>">Add all</button>
<button class="btn" data-clipboard-text="<?= $delete; ?>">Drop all</button>
<p id="log"></p>

<script>

$(function(){
	var clipboard = new Clipboard('.btn');
	
	clipboard.on('success', function(e) {
		if(e.trigger.textContent == 'Add all')
			$('#log').text('Skrypt dodaj w schowku');
		else if(e.trigger.textContent == 'Drop all') 
			$('#log').text('Skrypt usuń w schowku');
// 		console.log(e.trigger.textContent);
		//e.clearSelection();
	});
});

</script>