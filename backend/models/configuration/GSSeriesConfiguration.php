<?php

namespace backend\models\configuration;

use backend\models\Camera;
use backend\models\GatewayVoip;
use backend\models\Host;
use backend\models\Virtual;

class GSSeriesConfiguration extends Configuration {
    
    function __construct($device, $parentDevice) {
        
        parent::__construct($device, $parentDevice);
        $this->mac = $device->mac;
    }
    
    function add() {
        $add = ' ';
        if ($this->device instanceof Host) {
            $add = "interface ethernet {$this->parentPortName}\n";
            $add .= "no service-acl input\n";
            $add .= "exit\n";
            $add .= "no ip access-list user{$this->parentPortNumber}\n";
            if ($this->device->smtp) $add .= "no ip access-list user{$this->parentPortNumber}smtp\n";
            $add .= "interface vlan {$this->vlanId}\n";
            $add .= "bridge address {$this->mac} permanent ethernet {$this->parentPortName}\n";
            $add .= "exit\n";
            $this->device->smtp ? $add .= "ip access-list user{$this->parentPortNumber}smtp\n" : $add .= "ip access-list user{$this->parentPortNumber}\n";
            $add .= "deny-udp any any any 68\n";
            if (!$this->device->smtp) $add .= "deny-tcp any any any 25\n";
            $add .= "permit any {$this->ip} 0.0.0.0 any\n";
            $add .= "permit-udp 0.0.0.0 0.0.0.0 68 any 67\n";
            $add .= "exit\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "switchport trunk allowed vlan remove all\n";
            $add .= "switchport mode access\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "description {$this->desc}\n";
            $this->device->smtp ? $add .= "service-acl input user{$this->parentPortNumber}smtp\n" : $add .= "service-acl input user{$this->parentPortNumber}\n";
            $add .= "traffic-shape 830000 8300000\n";
            $add .= "rate-limit 938000\n";
            $add .= "port security mode lock\n";
            $add .= "port security discard\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree bpduguard\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "exit\n";
            $add .= "copy r s\n";
            $add .= "y\n";
        } elseif ($this->device instanceof GatewayVoip) {
            $add = "interface vlan {$this->vlanId}\n";
            $add .= "bridge address {$this->mac} permanent ethernet {$this->parentPortName}\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "no service-acl input\n";
            $add .= "exit\n";
            $add .= "no ip access-list voip{$this->parentPortNumber}\n";
            $add .= "ip access-list voip{$this->parentPortNumber}\n";
            $add .= "deny-udp any any any 68\n";
            $add .= "permit any {$this->ip} 0.0.0.0 213.5.208.0 0.0.0.63\n";
            $add .= "permit any {$this->ip} 0.0.0.0 213.5.208.128 0.0.0.63\n";
            $add .= "permit any {$this->ip} 0.0.0.0 10.111.0.0 0.0.255.255\n";
            $add .= "permit-udp 0.0.0.0 0.0.0.0 68 any 67\n";
            $add .= "exit\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "switchport trunk allowed vlan remove all\n";
            $add .= "switchport mode access\n";
            $add .= "description {$this->desc}\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree bpduguard\n";
            $add .= "service-acl input voip{$this->parentPortNumber}\n";
            $add .= "port security mode lock\n";
            $add .= "port security discard\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "exit\n";
            $add .= "copy r s\n";
            $add .= "y\n";
        } elseif ($this->device instanceof Camera) {
            $add = "interface vlan {$this->vlanId}\n";
            $add .= "bridge address {$this->mac} permanent ethernet {$this->parentPortName}\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "no service-acl input\n";
            $add .= "exit\n";
            $add .= "no ip access-list voip{$this->parentPortNumber}\n";
            $add .= "ip access-list cam{$this->parentPortNumber}\n";
            $add .= "deny-udp any any any 68\n";
            $add .= "permit any {$this->ip} 0.0.0.0 213.5.208.128 0.0.0.63\n";
            $add .= "permit any {$this->ip} 0.0.0.0 192.168.5.0 0.0.0.255\n";
            $add .= "permit any {$this->ip} 0.0.0.0 10.111.0.0 0.0.255.255\n";
            $add .= "permit-udp 0.0.0.0 0.0.0.0 68 any 67\n";
            $add .= "exit\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "switchport trunk allowed vlan remove all\n";
            $add .= "switchport mode access\n";
            $add .= "description {$this->desc}\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree bpduguard\n";
            $add .= "service-acl input cam{$this->parentPortNumber}\n";
            $add .= "port security mode lock\n";
            $add .= "port security discard\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "exit\n";
            $add .= "cop r s\n";
            $add .= "y\n";
        } elseif ($this->device instanceof Virtual) {
            $add = "interface ethernet {$this->parentPortName}\n";
            $add .= "no service-acl input\n";
            $add .= "exit\n";
            $add .= "no ip access-list user{$this->parentPortNumber}\n";
            $add .= "interface vlan {$this->vlanId}\n";
            $add .= "bridge address {$this->mac} permanent ethernet {$this->parentPortName}\n";
            $add .= "exit\n";
            $add .= "ip access-list user{$this->parentPortNumber}\n";
            $add .= "deny-udp any any any 68\n";
            $add .= "permit any {$this->ip} 0.0.0.0 any\n";
            $add .= "permit-udp 0.0.0.0 0.0.0.0 68 any 67\n";
            $add .= "exit\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "switchport trunk allowed vlan remove all\n";
            $add .= "switchport mode access\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "description {$this->desc}\n";
            $add .= "service-acl input user{$this->parentPortNumber}\n";
            $add .= "traffic-shape 830000 8300000\n";
            $add .= "rate-limit 938000\n";
            $add .= "port security mode lock\n";
            $add .= "port security discard\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree bpduguard\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "exit\n";
            $add .= "copy r s\n";
            $add .= "y\n";
        }
    return $add;
    }

    function drop($auto) {
        $drop = ' '; 
        if ($this->device instanceof Host) {
            $drop = "interface vlan {$this->vlanId}\n";
            $drop .= "no bridge address {$this->mac}\n";
            $drop .= "exit\n";
            $drop .= "interface ethernet {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "no service-acl input\n";
            $drop .= "no traffic-shape\n";
            $drop .= "no rate-limit\n";
            $drop .= "no port security\n";
            $drop .= "sw a v 555\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "ip access-list user{$this->parentPortNumber}\n";
            $drop .= "no ip access-list user{$this->parentPortNumber}\n";
            if ($this->device->smtp) $drop .= "no ip access-list user{$this->parentPortNumber}smtp\n";
            $drop .= "exit\n";
            if (!$auto) $drop .= "copy r s\n";
            if (!$auto) $drop .= "y\n";
        } elseif ($this->device instanceof GatewayVoip) {
            $drop = "interface vlan {$this->vlanId}\n";
            $drop .= "no bridge address {$this->mac}\n";
            $drop .= "interface ethernet {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "switchport access vlan 555\n";
            $drop .= "no service-acl input\n";
            $drop .= "no port security\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "no ip access-list voip{$this->parentPortNumber}\n";
            $drop .= "exit\n";
            $drop .= "copy r s\n";
            $drop .= "y\n";
        } elseif ($this->device instanceof Camera) {
            $drop = "interface vlan {$this->vlanId}\n";
            $drop .= "no bridge address {$this->mac}\n";
            $drop .= "interface ethernet {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "switchport access vlan 555\n";
            $drop .= "no service-acl input\n";
            $drop .= "no port security\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "no ip access-list cam{$this->parentPortNumber}\n";
            $drop .= "exit\n";
            $drop .= "cop r s\n"; 
            $drop .= "y\n";
        } elseif ($this->device instanceof Virtual) {
            $drop = "interface vlan {$this->vlanId}\n";
            $drop .= "no bridge address {$this->mac}\n";
            $drop .= "exit\n";
            $drop .= "interface ethernet {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "no service-acl input\n";
            $drop .= "no traffic-shape\n";
            $drop .= "no rate-limit\n";
            $drop .= "no port security\n";
            $drop .= "sw a v 555\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "no ip access-list user{$this->parentPortNumber}\n";
            $drop .= "exit\n";
            $drop .= "copy r s\n";
            $drop .= "y\n";
        }
    return $drop;
    }
    
    function changeMac($newMac) {
        $change = "interface ethernet {$this->parentPortName}\n";
        $change .= "shutdown\n";
        $change .= "no port security\n";
        $change .= "exit\n";
        $change .= "interface vlan {$this->vlanId}\n";
        $change .= "no bridge address {$this->mac}\n";
        $change .= "bridge address {$newMac} permanent ethernet {$this->parentPortName}\n";
        $change .= "exit\n";
        $change .= "interface ethernet {$this->parentPortName}\n";
        $change .= "port security mode lock\n";
        $change .= "port security discard\n";
        $change .= "no shutdown\n";
        $change .= "exit\n";
        $change .= "exit\n";
        $change .= "copy r s\n";
        $change .= "y\n";
    return $change;
    }
}