<?php 

use backend\models\Device;


$parent = $modelDevice->modelTree[0]->parent_device;	//id parent
$parentPortIndex = $modelDevice->modelTree[0]->parent_port; //index port

$modelDeviceParent = Device::findOne($parent);
$arPortsParent = $modelDeviceParent->modelModel->port;
// var_dump($arPortsParent);

if($modelDeviceParent->modelModel->config == 1){

$add = 'interface ethernet ' . $arPortsParent[$parentPortIndex] . '
no service-acl input' . '
exit' . '
no ip access-list user' . ($parentPortIndex + 1) . '
no ip access-list user' . ($parentPortIndex + 1) . '
interface vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
bridge address ' . $modelDevice->mac . ' permanent ethernet ' . $arPortsParent[$parentPortIndex] . '		
exit' . '
ip access-list user' . ($parentPortIndex + 1) . '
deny-udp any any any 68' . '
deny-tcp any any any 25' . '
permit any ' . $modelIps[0]->ip . ' 0.0.0.0 any' . '
permit-udp 0.0.0.0 0.0.0.0 68 any 67' . '
exit' . '
interface ethernet ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
switchport trunk allowed vlan remove all' . '
switchport mode access' . '
switchport access vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
description ' . $modelDevice->modelAddress->shortAddress . '
service-acl input user' . ($parentPortIndex + 1) . '
traffic-shape 520000 5200000' . '
rate-limit 800000' . '		
port security mode lock' . '
port security discard' . '
spanning-tree portfast' . '
spanning-tree bpduguard' . '
no shutdown' . '
exit' . ' 
exit' . '
copy r s' . '
y' . '
';


$delete = 'interface vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
no bridge address ' . $modelDevice->mac . '
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
} elseif($modelDeviceParent->modelModel->config == 2){
	
if($modelDeviceParent->modelAddress->modelShortStreet->config == 1){	
	
$add = 'interface ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
no switchport port-security' . '
switchport port-security violation protect' . '
switchport port-security maximum 0' . '
switchport port-security' . '
description ' . $modelDevice->modelAddress->shortAddress . '
egress-rate-limit 508032k' . '
service-policy input 501M' . '
access-group anyuser' . '
switchport access vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
spanning-tree portfast' . '
spanning-tree portfast bpdu-guard enable' . '
no shutdown' . '
exit' . '
mac address-table static ' . preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac)) . ' forward interface ' . $arPortsParent[$parentPortIndex] . ' vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
exit' . '
wr' . '
';

$delete = 'no mac address-table static ' . preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac)) . ' forward interface ' . $arPortsParent[$parentPortIndex] . ' vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
interface ' . $arPortsParent[$parentPortIndex] . '
no switchport port-security' . '
no service-policy input 501M' . '		
no egress-rate-limit' . '	
no access-group anyuser' . '			
switchport access vlan 555' . '	
exit' . '
do clear ip dhcp snooping binding int ' . $arPortsParent[$parentPortIndex] . '		
exit' . '
wr' . '
';

} elseif($modelDeviceParent->modelAddress->modelShortStreet->config == 2){

$add = 'interface ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
no switchport port-security' . '
switchport port-security violation protect' . '
switchport port-security maximum 0' . '
switchport port-security' . '
description ' . $modelDevice->modelAddress->shortAddress . '
egress-rate-limit 508032k' . '
service-policy input internet-user-501M' . '
access-group internet-user' . '
no ip igmp trusted all' . '		
switchport access vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
spanning-tree portfast' . '
spanning-tree portfast bpdu-guard enable' . '
no shutdown' . '
exit' . '
mac address-table static ' . preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac)) . ' forward interface ' . $arPortsParent[$parentPortIndex] . ' vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
exit' . '
wr' . '
';	

$addiptv = 'interface ' . $arPortsParent[$parentPortIndex] . '
shutdown' . '
no switchport port-security' . '
switchport port-security violation protect' . '
switchport port-security maximum 0' . '
switchport port-security' . '
description ' . $modelDevice->modelAddress->shortAddress . '
egress-rate-limit 508032k' . '
service-policy input iptv-user-501M' . '
access-group iptv-user' . '
no ip igmp trusted all' . '
switchport access vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
spanning-tree portfast' . '
spanning-tree portfast bpdu-guard enable' . '
no shutdown' . '
exit' . '
mac address-table static ' . preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac)) . ' forward interface ' . $arPortsParent[$parentPortIndex] . ' vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
exit' . '
wr' . '
';

$delete = 'no mac address-table static ' . preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac)) . ' forward interface ' . $arPortsParent[$parentPortIndex] . ' vlan ' . $modelIps[0]->modelSubnet->modelVlan->id . '
interface ' . $arPortsParent[$parentPortIndex] . '
no switchport port-security' . '
no service-policy input internet-user-501M' . '
no service-policy input iptv-user-501M' . '		
no egress-rate-limit' . '
no ip igmp trust all' . '		
no access-group internet-user' . '
no access-group iptv-user' . '		
switchport access vlan 555' . '
exit' . '
do clear ip dhcp snooping binding int ' . $arPortsParent[$parentPortIndex] . '
exit' . '
wr' . '
';
}
}
?>

<a href="ssh://<?= $modelDeviceParent->modelIps[0]->ip; ?>:22222">Zaloguj</a>
<button class="btn" data-clipboard-text="<?= $add; ?>">Dodaj</button>
<?php if($modelDeviceParent->modelAddress->modelShortStreet->config == 2) :?>
	<button class="btn" data-clipboard-text="<?= $addiptv; ?>">Dodaj IPTV</button>
<?php endif;?>

<button class="btn" data-clipboard-text="<?= $delete; ?>">Usuń</button>
<p id="log"></p>

<script>

$(function(){
	var clipboard = new Clipboard('.btn');
	
	clipboard.on('success', function(e) {
		if(e.trigger.textContent == 'Dodaj')
			$('#log').text('Skrypt dodaj w schowku');
		else if(e.trigger.textContent == 'Dodaj IPTV') 
			$('#log').text('Skrypt dodaj IPTV w schowku');
		else if(e.trigger.textContent == 'Usuń') 
			$('#log').text('Skrypt usuń w schowku');
// 		console.log(e.trigger.textContent);
		//e.clearSelection();
	});
});

</script>