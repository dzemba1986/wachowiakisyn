<?php

namespace backend\models\configuration;

use backend\models\Camera;
use backend\models\GatewayVoip;
use backend\models\Host;

class XSeriesConfiguration extends Configuration {
    
    function __construct($device, $parentDevice) {
        
        parent::__construct($device, $parentDevice);
        $this->mac = preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace(':', '', $device->mac));
    }
    
    function add() {
        
        if ($this->device instanceof Host) {

            if (strpos($this->device->parentIp, '172.') === 0) {
$add = <<<ADD
interface {$this->parentPortName}
shutdown
no switchport port-security
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
description {$this->desc}
egress-rate-limit 508032k
service-policy input 501M
access-group anyuser
switchport access vlan {$this->vlanId}
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
no shutdown
exit
mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}
exit
wr

ADD;
            } else {
                if (count($this->device->connections) == 2) {
$add = <<<ADD
interface {$this->parentPortName}
shutdown
no switchport port-security
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
description {$this->desc}
egress-rate-limit 508032k
service-policy input iptv-user-501M
access-group iptv-user
no ip igmp trusted all
ip igmp trusted report
switchport access vlan {$this->vlanId}
no shutdown
exit
mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}
exit
wr

ADD;
                } elseif ($this->device->connections[0]->type_id == 1) {
$add = <<<ADD
interface {$this->parentPortName}
shutdown
no switchport port-security
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
description {$this->desc}
egress-rate-limit 508032k
service-policy input internet-user-501M
access-group internet-user
no ip igmp trusted all
ip igmp trusted report
switchport access vlan {$this->vlanId}
no shutdown
exit
mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}
exit
wr

ADD;
                } elseif ($this->device->connections[0]->type_id == 3) {
$add = <<<ADD
interface {$this->parentPortName}
shutdown
no switchport port-security
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
description {$this->desc}
egress-rate-limit 508032k
service-policy input iptv-only-501M
access-group iptv-only
no ip igmp trusted all
ip igmp trusted report
switchport access vlan {$this->vlanId}
no shutdown
exit
mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}
exit
wr

ADD;
                }
            }
        } elseif ($this->device instanceof GatewayVoip) {

$add = <<<ADD
access-list hardware voip{$this->parentPortNumber}
deny udp any any eq 68
permit ip {$this->ip} 0.0.0.0 213.5.208.0 0.0.0.63
permit ip {$this->ip} 0.0.0.0 213.5.208.128 0.0.0.63
permit ip {$this->ip} 0.0.0.0 10.111.0.0 0.0.255.255
permit udp 0.0.0.0 0.0.0.0 eq 68 any eq 67
deny ip any any
exit
interface {$this->parentPortName}
shutdown
description {$this->desc}
switchport access vlan {$this->vlanId}
access-group voip{$this->parentPortNumber}
spanning-tree portfast
spanning-tree portfast bpdu-guard enable
no shutdown
exit
exit
wr

ADD;
        } elseif ($this->device instanceof Camera) {

$add = <<<ADD
interface {$this->parentPortName}
shutdown
description {$this->desc}
switchport access vlan {$this->vlanId}
access-group camera
switchport port-security violation protect
switchport port-security maximum 0
switchport port-security
no shutdown
exit
mac address-table static {$this->mac} forward interface port1.0.1 vlan 3
exit
wr

ADD;
        } else $add = '';

    return $add;
    }

    function drop() {
        
        if ($this->device instanceof Host) {
            if (strpos($this->device->parentIp, '172.') === 0) {
$drop = <<<DELETE
no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}
interface {$this->parentPortName}
no switchport port-security
no service-policy input 501M
no egress-rate-limit
no access-group anyuser
switchport access vlan 555
exit
do clear ip dhcp snooping binding int {$this->parentPortName}
exit
wr

DELETE;
            } else {
$drop = <<<DELETE
no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}
int {$this->parentPortName}
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
do clear ip dhcp snooping binding interface {$this->parentPortName}
exit

DELETE;
            }

        } elseif ($this->device instanceof GatewayVoip) {

$drop = <<<DELETE
interface {$this->parentPortName}
shutdown
no access-group voip{$this->parentPortNumber}
switchport access vlan 555
no shutdown
exit
no access-list hardware voip{$this->parentPortNumber}
exit
wr

DELETE;
            
        } elseif ($this->device instanceof Camera) {

$drop = <<<DELETE
no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}
interface {$this->parentPortName}
shutdown
no access-group camera
no description
no switchport port-security
switchport access vlan 555
no shutdown
exit
exit
wr

DELETE;
            
        }  else $drop = '';
        
    return $drop;
    }
    
    function changeMac($newMac) {
        
        if ($this->device instanceof Host) {

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
        } else $change = '';

    return $change;
    }
}