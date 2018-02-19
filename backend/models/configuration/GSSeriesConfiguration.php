<?php

namespace backend\models\configuration;

use backend\models\Camera;
use backend\models\GatewayVoip;
use backend\models\Host;

class GSSeriesConfiguration extends Configuration {
    
    function __construct($device, $parentDevice) {
        
        parent::__construct($device, $parentDevice);
        $this->mac = $device->mac;
    }
    
    function add() {
        $add = ' ';
        if ($this->device instanceof Host) {
            
$add = <<<ADD
interface ethernet {$this->parentPortName}
no service-acl input
exit
no ip access-list user{$this->parentPortNumber}
no ip access-list user{$this->parentPortNumber}
interface vlan {$this->vlanId}
bridge address {$this->mac} permanent ethernet {$this->parentPortName}
exit
ip access-list user{$this->parentPortNumber}
deny-udp any any any 68
deny-tcp any any any 25
permit any {$this->ip} 0.0.0.0 any
permit-udp 0.0.0.0 0.0.0.0 68 any 67
exit
interface ethernet {$this->parentPortName}
shutdown
switchport trunk allowed vlan remove all
switchport mode access
switchport access vlan {$this->vlanId}
description {$this->desc}
service-acl input user{$this->parentPortNumber}
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
        } elseif ($this->device instanceof GatewayVoip) {

$add = <<<ADD
ip access-list voip{$this->parentPortNumber}
deny-udp any any any 68
permit any {$this->ip} 0.0.0.0 213.5.208.0 0.0.0.63
permit any {$this->ip} 0.0.0.0 213.5.208.128 0.0.0.63
permit any {$this->ip} 0.0.0.0 10.111.0.0 0.0.255.255
permit-udp 0.0.0.0 0.0.0.0 68 any 67
exit
interface ethernet {$this->parentPortName}
shutdown
switchport trunk allowed vlan remove all
switchport mode access
description {$this->desc}
switchport access vlan {$this->vlanId}
spanning-tree portfast
spanning-tree bpduguard
service-acl input voip{$this->parentPortNumber}
no shutdown
exit
exit
copy r s
y

ADD;
        } elseif ($this->device instanceof Camera) {

$add = <<<ADD
interface vlan {$this->vlanId}
bridge address {$this->mac} permanent ethernet {$this->parentPortName}
ip access-list cam{$this->parentPortNumber}
deny-udp any any any 68
permit any {$this->ip} 0.0.0.0 213.5.208.128 0.0.0.63
permit any {$this->ip} 0.0.0.0 192.168.5.0 0.0.0.255
permit-udp 0.0.0.0 0.0.0.0 68 any 67
exit
interface ethernet {$this->parentPortName}
shutdown
description {$this->desc}
switchport access vlan {$this->vlanId}
service-acl input cam{$this->parentPortNumber}
port security mode lock
port security discard
no shutdown
exit
exit
cop r s
y

ADD;
        }

    return $add;
    }

    function drop() {
        $drop = ' '; 
        if ($this->device instanceof Host) {
            
$drop = <<<DELETE
interface vlan {$this->vlanId}
no bridge address {$this->mac}
exit
interface ethernet {$this->parentPortName}
shutdown
no service-acl input
no traffic-shape
no rate-limit
no port security
sw a v 555
no shutdown
exit
no ip access-list user{$this->parentPortNumber}
no ip access-list user{$this->parentPortNumber}
exit
copy r s
y

DELETE;
        } elseif ($this->device instanceof GatewayVoip) {

$drop = <<<DELETE
interface ethernet {$this->parentPortName}
shutdown
switchport access vlan 555
no service-acl input
no shutdown
exit
no ip access-list voip{$this->parentPortNumber}
no ip access-list voip{$this->parentPortNumber}
exit
copy r s
y

DELETE;
        } elseif ($this->device instanceof Camera) {

$drop = <<<DELETE
interface vlan {$this->vlanId}
no bridge address {$this->mac}
interface ethernet {$this->parentPortName}
shutdown
no description
switchport access vlan 555
no service-acl input
no port security
no shutdown
exit
no ip access-list cam{$this->parentPortNumber}
exit
cop r s 
y

DELETE;
            
        }
    
    return $drop;
    }
    
    function changeMac($newMac) {

        if ($this->device instanceof Host) {

$change = <<<CHANGE
interface ethernet {$this->parentPortName}
shutdown
no port security
exit
interface vlan {$this->vlanId}
no bridge address {$this->mac}
bridge address {$newMac} permanent ethernet {$this->parentPortName}
exit
interface ethernet {$this->parentPortName}
port security mode lock
port security discard
no shutdown
exit
exit
copy r s
y

CHANGE;
        } else $change = ' ';

    return $change;
    }
}