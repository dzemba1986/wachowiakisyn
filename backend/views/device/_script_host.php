<?php 

use backend\models\Device;
$parent = $modelDevice->modelTree[0]->parent_device;	//id parent
$parentPortIndex = $modelDevice->modelTree[0]->parent_port; //index port
$portNumber = $parentPortIndex + 1;

$modelDeviceParent = Device::findOne($parent);
$arPortsParent = $modelDeviceParent->modelModel->port;

if($modelDeviceParent->modelModel->config == 1){

$add = <<<ADD
interface ethernet {$arPortsParent[$parentPortIndex]}
no service-acl input
exit
no ip access-list user{$portNumber}
no ip access-list user{$portNumber}
interface vlan {$modelIps[0]->modelSubnet->modelVlan->id}
bridge address {$modelDevice->mac} permanent ethernet {$arPortsParent[$parentPortIndex]}
exit
ip access-list user{$portNumber}
deny-udp any any any 68
deny-tcp any any any 25
permit any {$modelIps[0]->ip} 0.0.0.0 any
permit-udp 0.0.0.0 0.0.0.0 68 any 67
exit
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
switchport trunk allowed vlan remove all
switchport mode access
switchport access vlan {$modelIps[0]->modelSubnet->modelVlan->id}
description {$modelDevice->modelAddress->toString(true)}
service-acl input user{$portNumber}
traffic-shape 520000 5200000
rate-limit 800000
port security mode lock
port security discard
spanning-tree portfast
spanning-tree bpduguard
no shutdown
exit
exit
copy r s
y

ADD;

$delete = <<<DELETE
interface vlan {$modelIps[0]->modelSubnet->modelVlan->id}
no bridge address {$modelDevice->mac}
exit
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
no service-acl input
no traffic-shape
no rate-limit
no port security
sw a v 555
no shutdown
exit
no ip access-list user{$portNumber}
no ip access-list user{$portNumber}
exit
copy r s
y

DELETE;

} elseif($modelDeviceParent->modelModel->config == 2){

$preg_replace = function($pattern, $replacement, $subject) {
	return preg_replace($pattern, $replacement, $subject);
};	
	
if($modelDeviceParent->modelAddress->getConfigMode() == 1){	
	
$add = <<<ADD
interface {$arPortsParent[$parentPortIndex]}
shutdown
no switchport port-security
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
description {$modelDevice->modelAddress->toString(true)}
egress-rate-limit 508032k
service-policy input 501M
access-group anyuser
switchport access vlan {$modelIps[0]->modelSubnet->modelVlan->id}
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
no shutdown
exit
mac address-table static {$preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac))} forward interface {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
exit
wr

ADD;

$delete = <<<DELETE
no mac address-table static {$preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac))} forward interface {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
interface {$arPortsParent[$parentPortIndex]}
no switchport port-security
no service-policy input 501M
no egress-rate-limit
no access-group anyuser
switchport access vlan 555
exit
do clear ip dhcp snooping binding int {$arPortsParent[$parentPortIndex]}
exit
wr

DELETE;

} elseif($modelDeviceParent->modelAddress->configMode == 2){

$add = <<<ADD
interface {$arPortsParent[$parentPortIndex]}
shutdown
no switchport port-security
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
description {$modelDevice->modelAddress->toString(true)}
egress-rate-limit 508032k
service-policy input internet-user-501M
access-group internet-user
no ip igmp trusted all		
switchport access vlan {$modelIps[0]->modelSubnet->modelVlan->id}
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
no shutdown
exit
mac address-table static {$preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac))} forward interface {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
exit
wr

ADD;

$addiptv = <<<ADDIPTV
interface {$arPortsParent[$parentPortIndex]}
shutdown
no switchport port-security
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
description {$modelDevice->modelAddress->toString(true)}
egress-rate-limit 508032k
service-policy input iptv-user-501M
access-group iptv-user
no ip igmp trusted all
ip igmp trusted report
switchport access vlan {$modelIps[0]->modelSubnet->modelVlan->id}
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
no shutdown
exit
mac address-table static {$preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac))} forward interface {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
exit
wr

ADDIPTV;

$onlyiptv = <<<ONLYIPTV
interface {$arPortsParent[$parentPortIndex]}
shutdown
no switchport port-security
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
description {$modelDevice->modelAddress->toString(true)}
egress-rate-limit 508032k
service-policy input iptv-only-501M
access-group iptv-only
no ip igmp trusted all
ip igmp trusted report
switchport access vlan {$modelIps[0]->modelSubnet->modelVlan->id}
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
no shutdown
exit
mac address-table static {$preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac))} forward interface {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
exit
wr

ONLYIPTV;

$delete = <<<DELETE
no mac address-table static {$preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $modelDevice->mac))} forward interface {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
interface {$arPortsParent[$parentPortIndex]}
no switchport port-security
no service-policy input internet-user-501M
no service-policy input iptv-user-501M
no service-policy input iptv-only-501M
no egress-rate-limit
no ip igmp trust all
no access-group internet-user
no access-group iptv-user
no access-group iptv-only
switchport access vlan 555
exit
do clear ip dhcp snooping binding int {$arPortsParent[$parentPortIndex]}
exit
wr

DELETE;
} 
} elseif($modelDeviceParent->modelModel->config == 5){
$preg_replace = function($pattern, $replacement, $subject) {
	return preg_replace($pattern, $replacement, $subject);
};	
	
if($modelDeviceParent->modelAddress->configMode == 2){
		
$add = <<<ADD
mac-address-table static {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} interface ethernet {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id} permanent
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->modelAddress->toString(true)}
ip igmp filter 7
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$modelIps[0]->modelSubnet->modelVlan->id} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$modelIps[0]->modelSubnet->modelVlan->id}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group internet-user in
service-policy input internet-user-501M
ip dhcp snooping max-number 1
ip source-guard sip-mac
ip source-guard mode mac
ip igmp query-drop
ip multicast-data-drop
loopback-detection
discard cdp
discard pvs
no shutdown
end
cop r s

ADD;

$add = <<<ADD
mac-address-table static {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} interface ethernet {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id} permanent
access-list IP extended internet-user{$portNumber}
deny TCP any any destination-port 25
permit host {$modelIps[0]->ip} any
deny any any
exit
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->modelAddress->toString(true)}
ip igmp filter 7
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$modelIps[0]->modelSubnet->modelVlan->id} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$modelIps[0]->modelSubnet->modelVlan->id}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group internet-user{$portNumber} in
service-policy input internet-user-501M
ip dhcp snooping max-number 1
ip igmp query-drop
ip multicast-data-drop
loopback-detection
discard cdp
discard pvs
no shutdown
end
cop r s


ADD;

		
$addiptv = <<<ADDIPTV
mac-address-table static {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} interface ethernet {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id} permanent
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->modelAddress->toString(true)}
ip igmp filter 8
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$modelIps[0]->modelSubnet->modelVlan->id} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$modelIps[0]->modelSubnet->modelVlan->id}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group iptv-user in
service-policy input iptv-user-501M
ip dhcp snooping max-number 1
ip source-guard sip-mac
ip source-guard mode mac
ip igmp query-drop
ip multicast-data-drop
loopback-detection
discard cdp
discard pvs
no shutdown
end
cop r s

ADDIPTV;

$addiptv = <<<ADDIPTV
mac-address-table static {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} interface ethernet {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id} permanent
access-list IP extended iptv-user{$portNumber}
deny TCP any any destination-port 25
permit host {$modelIps[0]->ip} any
deny any any
exit
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->modelAddress->toString(true)}
ip igmp filter 8
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$modelIps[0]->modelSubnet->modelVlan->id} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$modelIps[0]->modelSubnet->modelVlan->id}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group iptv-user{$portNumber} in
service-policy input iptv-user-501M
ip dhcp snooping max-number 1
ip igmp query-drop
ip multicast-data-drop
loopback-detection
discard cdp
discard pvs
no shutdown
end
cop r s


ADDIPTV;
		
$onlyiptv = <<<ONLYIPTV
mac-address-table static {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} interface ethernet {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id} permanent
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->modelAddress->toString(true)}
ip igmp filter 8
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$modelIps[0]->modelSubnet->modelVlan->id} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$modelIps[0]->modelSubnet->modelVlan->id}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group iptv-only in
service-policy input iptv-user-501M
ip dhcp snooping max-number 1
ip source-guard sip-mac
ip source-guard mode mac
ip igmp query-drop
ip multicast-data-drop
loopback-detection
discard cdp
discard pvs
no shutdown
end
cop r s

ONLYIPTV;
		
$onlyiptv = <<<ONLYIPTV
mac-address-table static {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} interface ethernet {$arPortsParent[$parentPortIndex]} vlan {$modelIps[0]->modelSubnet->modelVlan->id} permanent
access-list IP extended iptv-only{$portNumber}
deny any any
exit
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
description {$modelDevice->modelAddress->toString(true)}
ip igmp filter 8
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$modelIps[0]->modelSubnet->modelVlan->id} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$modelIps[0]->modelSubnet->modelVlan->id}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group iptv-only{$portNumber} in
service-policy input iptv-user-501M
ip dhcp snooping max-number 1
ip igmp query-drop
ip multicast-data-drop
loopback-detection
discard cdp
discard pvs
no shutdown
end
cop r s


ONLYIPTV;

$delete = <<<DELETE
no mac-address-table static {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
ip igmp filter 7
no port security
rate-limit input 1000000
no rate-limit input
rate-limit output 1000000
no rate-limit output
switchport allowed vlan add 555 untagged
switchport ingress-filtering
switchport mode access
switchport native vlan 555
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
spanning-tree bpdu-guard
queue mode strict
no ip access-group iptv-user in
no ip access-group iptv-user-smtp in
no ip access-group internet-user in
no ip access-group internet-user-smtp in
no ip access-group iptv-only in
no service-policy input iptv-user-501M
no service-policy input internet-user-501M
no ip dhcp snooping max-number
no ip source-guard
ip source-guard mode acl
ip igmp query-drop
ip multicast-data-drop
loopback-detection
discard cdp
discard pvs
no shutdown
end
cop r s

DELETE;

$delete = <<<DELETE
no mac-address-table static {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} vlan {$modelIps[0]->modelSubnet->modelVlan->id}
interface ethernet {$arPortsParent[$parentPortIndex]}
shutdown
ip igmp filter 7
no port security
rate-limit input 1000000
no rate-limit input
rate-limit output 1000000
no rate-limit output
switchport allowed vlan add 555 untagged
switchport ingress-filtering
switchport mode access
switchport native vlan 555
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
spanning-tree bpdu-guard
queue mode strict
no ip access-group iptv-user{$portNumber} in
no ip access-group iptv-user-smtp{$portNumber} in
no ip access-group internet-user{$portNumber} in
no ip access-group internet-user-smtp{$portNumber} in
no ip access-group iptv-only{$portNumber} in
no service-policy input iptv-user-501M
no service-policy input internet-user-501M
no ip dhcp snooping max-number
no ip source-guard
ip source-guard mode acl
ip igmp query-drop
ip multicast-data-drop
loopback-detection
discard cdp
discard pvs
no shutdown
exit
no access-list IP extended iptv-user{$portNumber}
no access-list IP extended iptv-user-smtp{$portNumber}
no access-list IP extended internet-user{$portNumber}
no access-list IP extended internet-user-smtp{$portNumber}
no access-list IP extended iptv-only{$portNumber}

exit
clear ip dhcp snooping binding {$preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace(':', '', $modelDevice->mac))} {$modelIps[0]->ip}
cop r s


DELETE;
}
}
?>
<?php if(isset($modelDeviceParent->modelIps[0])) : ?>
<a href="ssh://<?= $modelDeviceParent->modelIps[0]->ip; ?>:22222">Zaloguj</a>
<?php endif; ?>

<button class="btn" data-clipboard-text="<?= $add; ?>">Dodaj NET</button>
<?php if($modelDeviceParent->modelAddress->configMode == 2) :?>
	<button class="btn" data-clipboard-text="<?= $addiptv; ?>">Dodaj NET+IPTV</button>
	<button class="btn" data-clipboard-text="<?= $onlyiptv; ?>">Dodaj IPTV</button>
<?php endif;?>

<button class="btn" data-clipboard-text="<?= $delete; ?>">Usuń</button>
<p id="log"></p>

<script>

$(function(){
	var clipboard = new Clipboard('.btn');
	
	clipboard.on('success', function(e) {
		if(e.trigger.textContent == 'Dodaj NET')
			$('#log').text('Skrypt dodaj w schowku');
		else if(e.trigger.textContent == 'Dodaj IPTV') 
			$('#log').text('Skrypt dodaj IPTV w schowku');
		else if(e.trigger.textContent == 'Dodaj NET+IPTV') 
			$('#log').text('Skrypt dodaj NET+IPTV w schowku');
		else if(e.trigger.textContent == 'Usuń') 
			$('#log').text('Skrypt usuń w schowku');
// 		console.log(e.trigger.textContent);
		//e.clearSelection();
	});
});

</script>