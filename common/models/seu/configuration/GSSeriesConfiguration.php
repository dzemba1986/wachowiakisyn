<?php

namespace common\models\seu\configuration;

use common\models\seu\devices\Camera;
use common\models\seu\devices\GatewayVoip;
use common\models\seu\devices\Host;
use common\models\seu\devices\Ups;
use common\models\seu\devices\Virtual;

class GSSeriesConfiguration extends Configuration {
    
    function __construct($device) {
        
        parent::__construct($device);
        $this->mac = $device->mac;
    }
    
    function add() {
        $add = ' ';
        if ($this->typeId == Host::TYPE) {
            
            $add = "interface ethernet {$this->parentPortName}\n";
            $add .= "no service-acl input\n";
            $add .= "exit\n";
            $add .= "no ip access-list user{$this->parentPortNumber}\n";
            if ($this->smtp) $add .= "no ip access-list user{$this->parentPortNumber}smtp\n";
            $add .= "interface vlan {$this->vlanId}\n";
            $add .= "bridge address {$this->mac} permanent ethernet {$this->parentPortName}\n";
            $add .= "exit\n";
            $this->smtp ? $add .= "ip access-list user{$this->parentPortNumber}smtp\n" : $add .= "ip access-list user{$this->parentPortNumber}\n";
            $add .= "deny-udp any any any 68\n";
            if (!$this->smtp) $add .= "deny-tcp any any any 25\n";
            $add .= "permit any {$this->ip} 0.0.0.0 any\n";
            $add .= "permit-udp 0.0.0.0 0.0.0.0 68 any 67\n";
            $add .= "exit\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "switchport trunk allowed vlan remove all\n";
            $add .= "switchport mode access\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "description {$this->desc}\n";
            $this->smtp ? $add .= "service-acl input user{$this->parentPortNumber}smtp\n" : $add .= "service-acl input user{$this->parentPortNumber}\n";
            $add .= "port security mode lock\n";
            $add .= "port security discard\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree bpduguard\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "exit\n";
            $add .= "copy r s\n";
            $add .= "y\n";
        } elseif ($this->typeId == GatewayVoip::TYPE) {
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
        } elseif ($this->typeId == Camera::TYPE) {
            $add = "interface vlan {$this->vlanId}\n";
            $add .= "bridge address {$this->mac} permanent ethernet {$this->parentPortName}\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "no service-acl input\n";
            $add .= "exit\n";
            $add .= "no ip access-list cam{$this->parentPortNumber}\n";
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
        } elseif ($this->typeId == Ups::TYPE) {
            $add = "interface vlan {$this->vlanId}\n";
            $add .= "bridge address {$this->mac} permanent ethernet {$this->parentPortName}\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "no service-acl input\n";
            $add .= "exit\n";
            $add .= "no ip access-list ups{$this->parentPortNumber}\n";
            $add .= "ip access-list ups{$this->parentPortNumber}\n";
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
            $add .= "service-acl input ups{$this->parentPortNumber}\n";
            $add .= "port security mode lock\n";
            $add .= "port security discard\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "exit\n";
            $add .= "cop r s\n";
            $add .= "y\n";
        } elseif ($this->typeId == Virtual::TYPE) {
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
        if ($this->typeId == Host::TYPE) {
            $drop = "interface vlan {$this->vlanId}\n";
            $drop .= "no bridge address {$this->mac}\n";
            $drop .= "exit\n";
            $drop .= "interface ethernet {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "no service-acl input\n";
            $drop .= "no port security\n";
            $drop .= "sw a v 555\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "ip access-list user{$this->parentPortNumber}\n";
            $drop .= "no ip access-list user{$this->parentPortNumber}\n";
            if ($this->smtp) $drop .= "no ip access-list user{$this->parentPortNumber}smtp\n";
            $drop .= "exit\n";
            if (!$auto) $drop .= "copy r s\n";
            if (!$auto) $drop .= "y\n";
            
            if ($auto) {
                $fileName = rand(1000, 9999) . '.txt';
                $fileConf = '/var/tftp/' . $fileName;
                file_put_contents($fileConf, $drop);
                
                if (snmpset(
                    $this->parentIp,
                    "1nn3c0mmun1ty",
                    ['1.3.6.1.4.1.89.87.2.1.3.1', '1.3.6.1.4.1.89.87.2.1.4.1', '1.3.6.1.4.1.89.87.2.1.6.1', '1.3.6.1.4.1.89.87.2.1.8.1', '1.3.6.1.4.1.89.87.2.1.12.1', '1.3.6.1.4.1.89.87.2.1.17.1'],
                    ['i', 'a', 's', 'i', 'i', 'i'],
                    [3, '172.20.4.18', $fileName, 1, 2, 4],
                    4000000
                )) {
                    sleep(1);
                    snmpset(
                        $this->parentIp,
                        "1nn3c0mmun1ty",
                        ['1.3.6.1.4.1.89.87.2.1.3.1', '1.3.6.1.4.1.89.87.2.1.7.1', '1.3.6.1.4.1.89.87.2.1.8.1', '1.3.6.1.4.1.89.87.2.1.12.1', '1.3.6.1.4.1.89.87.2.1.17.1'],
                        ['i', 'i', 'i', 'i', 'i'],
                        [1, 2, 1, 3, 4],
                        4000000
                    );
                }
                
                exec('rm ' . $fileConf);
            }
            
        } elseif ($this->typeId == GatewayVoip::TYPE) {
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
        } elseif ($this->typeId == Camera::TYPE) {
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
        } elseif ($this->typeId == Ups::TYPE) {
            $drop = "interface vlan {$this->vlanId}\n";
            $drop .= "no bridge address {$this->mac}\n";
            $drop .= "interface ethernet {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "switchport access vlan 555\n";
            $drop .= "no service-acl input\n";
            $drop .= "no port security\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "no ip access-list ups{$this->parentPortNumber}\n";
            $drop .= "exit\n";
            $drop .= "cop r s\n";
            $drop .= "y\n";
        } elseif ($this->typeId == Virtual::TYPE) {
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