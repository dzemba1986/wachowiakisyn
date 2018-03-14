<?php

namespace backend\models\configuration;

use backend\models\Camera;
use backend\models\GatewayVoip;
use backend\models\Host;

class ECSeriesConfiguration extends Configuration {
    
    function __construct($device, $parentDevice) {
        
        parent::__construct($device, $parentDevice);
        $this->mac = preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace([':', '.', '-'], '', $device->mac));
        
    }
    
    function add() {
        $add = ' ';        
        if ($this->device instanceof Host) {

            if (count($this->device->connections) == 2) {
$add = <<<ADD
mac-address-table static {$this->mac} interface ethernet {$this->parentPortName}  vlan {$this->vlanId}  permanent
access-list IP extended iptv-user{$this->parentPortNumber}
deny TCP any any destination-port 25
permit host {$this->ip} any
deny any any
exit
interface ethernet {$this->parentPortName}
shutdown
description {$this->desc}
ip igmp filter 8
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$this->vlanId} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$this->vlanId}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group iptv-user{$this->parentPortNumber} in
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

ADD;
            } elseif (count($this->device->connections) == 1) {
            
                if ($this->device->connections[0]->type_id == 1) {
$add = <<<ADD
mac-address-table static {$this->mac} interface ethernet {$this->parentPortName}  vlan {$this->vlanId}  permanent
access-list IP extended internet-user{$this->parentPortNumber}
deny TCP any any destination-port 25
permit host {$this->ip} any
deny any any
exit
interface ethernet {$this->parentPortName}
shutdown 
description {$this->desc}
ip igmp filter 7
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$this->vlanId} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$this->vlanId}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group internet-user{$this->parentPortNumber} in
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
                } elseif ($this->device->connections[0]->type_id == 3) {
$add = <<<ADD
mac-address-table static {$this->mac} interface ethernet {$this->parentPortName}  vlan {$this->vlanId}  permanent
access-list IP extended iptv-only{$this->parentPortNumber}
deny any any
exit
interface ethernet {$this->parentPortName} 
shutdown
description {$this->desc}
ip igmp filter 8
port security
rate-limit input 812000
rate-limit output 560000
switchport allowed vlan add {$this->vlanId} untagged
switchport ingress-filtering
switchport mode access
switchport native vlan {$this->vlanId}
switchport allowed vlan remove 1
spanning-tree edge-port
spanning-tree bpdu-filter
no spanning-tree bpdu-guard
queue mode strict
ip access-group iptv-only{$this->parentPortNumber} in
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

ADD;
                }
            }
        } elseif ($this->device instanceof GatewayVoip) {

$add = <<<ADD

ADD;
        } elseif ($this->device instanceof Camera) {

$add = <<<ADD

ADD;
        }

    return $add;
    }

    function drop() {
        $drop = ' ';        
        if ($this->device instanceof Host) {
            
$drop = <<<DELETE
no mac-address-table static {$this->mac} vlan {$this->vlanId}
interface ethernet {$this->parentPortName}
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
no ip access-group iptv-user{$this->parentPortNumber} in
no ip access-group iptv-user-smtp{$this->parentPortNumber} in
no ip access-group internet-user{$this->parentPortNumber} in
no ip access-group internet-user-smtp{$this->parentPortNumber} in
no ip access-group iptv-only{$this->parentPortNumber} in
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
no access-list IP extended iptv-user{$this->parentPortNumber}
no access-list IP extended iptv-user-smtp{$this->parentPortNumber}
no access-list IP extended internet-user{$this->parentPortNumber}
no access-list IP extended internet-user-smtp{$this->parentPortNumber}
no access-list IP extended iptv-only{$this->parentPortNumber}
exit
clear ip dhcp snooping binding {$this->mac} {$this->ip}
cop r s

DELETE;

        } elseif ($this->device instanceof GatewayVoip) {

$drop = <<<DELETE

DELETE;
            
        } elseif ($this->device instanceof Camera) {

$drop = <<<DELETE

DELETE;
            
        }
        
    return $drop;
    }
    
    function changeMac($newMac) {
        
        $newMac = preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace([':', '.', '-'], '', $newMac));
        
        $change = ' ';

$change = <<<CHANGE
interface {$this->parentPortName}
shutdown
no switchport port-security
exit
no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}
mac address-table static {$newMac} forward interface {$this->parentPortName} vlan {$this->vlanId}
exit
clear ip dhcp snooping binding {$this->ip}
configure terminal
interface {$this->parentPortName}
switchport port-security
no shutdown
exit
exit
wr	

CHANGE;

    return $change;
    }
}