<?php

namespace common\models\seu\configuration;

use common\models\seu\devices\Camera;
use common\models\seu\devices\GatewayVoip;
use common\models\seu\devices\Host;
use common\models\seu\devices\Ups;
use common\models\seu\devices\Virtual;

class ECSeriesConfiguration extends Configuration {
    
    function __construct($device) {
        
        parent::__construct($device);
        $this->mac = preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace([':', '.', '-'], '', $device->mac));
    }
    
    function add() {
        $add = ' ';        
        if ($this->typeId == Host::TYPE) {

            if (count($this->device->connections) == 2) {
                $add = "mac-address-table static {$this->mac} interface ethernet {$this->parentPortName}  vlan {$this->vlanId}  permanent\n";
                $add .= "access-list IP extended iptv-user{$this->parentPortNumber}\n";
                $add .= "deny TCP any any destination-port 25\n";
                $add .= "permit host {$this->ip} any\n";
                $add .= "deny any any\n";
                $add .= "exit\n";
                $add .= "interface ethernet {$this->parentPortName}\n";
                $add .= "shutdown\n";
                $add .= "description {$this->desc}\n";
                $add .= "ip igmp filter 8\n";
                $add .= "port security\n";
                $add .= "rate-limit input 890000\n";
                $add .= "rate-limit output 840000\n";
                $add .= "switchport allowed vlan add {$this->vlanId} untagged\n";
                $add .= "switchport ingress-filtering\n";
                $add .= "switchport mode access\n";
                $add .= "switchport native vlan {$this->vlanId}\n";
                $add .= "switchport allowed vlan remove 1\n";
                $add .= "spanning-tree spanning-disabled\n";
                $add .= "no spanning-tree bpdu-guard\n";
                $add .= "queue mode strict\n";
                $add .= "ip access-group iptv-user{$this->parentPortNumber} in\n";
                $add .= "service-policy input iptv-user-x\n";
                $add .= "ip dhcp snooping max-number 1\n";
                $add .= "ip igmp query-drop\n";
                $add .= "ip multicast-data-drop\n";
                $add .= "loopback-detection\n";
                $add .= "discard cdp\n";
                $add .= "discard pvs\n";
                $add .= "no shutdown\n";
                $add .= "end\n";
                $add .= "cop r s\n";
            } elseif (count($this->device->connections) == 1) {
                if ($this->device->connections[0]->type_id == 1) {
                    $add = "mac-address-table static {$this->mac} interface ethernet {$this->parentPortName}  vlan {$this->vlanId}  permanent\n";
                    $add .= "access-list IP extended internet-user{$this->parentPortNumber}\n";
                    $add .= "deny TCP any any destination-port 25\n";
                    $add .= "permit host {$this->ip} any\n";
                    $add .= "deny any any\n";
                    $add .= "exit\n";
                    $add .= "interface ethernet {$this->parentPortName}\n";
                    $add .= "shutdown\n"; 
                    $add .= "description {$this->desc}\n";
                    $add .= "ip igmp filter 7\n";
                    $add .= "port security\n";
                    $add .= "rate-limit input 890000\n";
                    $add .= "rate-limit output 840000\n";
                    $add .= "switchport allowed vlan add {$this->vlanId} untagged\n";
                    $add .= "switchport ingress-filtering\n";
                    $add .= "switchport mode access\n";
                    $add .= "switchport native vlan {$this->vlanId}\n";
                    $add .= "switchport allowed vlan remove 1\n";
                    $add .= "spanning-tree spanning-disabled\n";
                    $add .= "no spanning-tree bpdu-guard\n";
                    $add .= "queue mode strict\n";
                    $add .= "ip access-group internet-user{$this->parentPortNumber} in\n";
                    $add .= "service-policy input internet-user-x\n";
                    $add .= "ip dhcp snooping max-number 1\n";
                    $add .= "ip igmp query-drop\n";
                    $add .= "ip multicast-data-drop\n";
                    $add .= "loopback-detection\n";
                    $add .= "discard cdp\n";
                    $add .= "discard pvs\n";
                    $add .= "no shutdown\n";
                    $add .= "end\n";
                    $add .= "cop r s\n";
                } elseif ($this->device->connections[0]->type_id == 3) {
                    $add = "mac-address-table static {$this->mac} interface ethernet {$this->parentPortName}  vlan {$this->vlanId}  permanent\n";
                    $add .= "access-list IP extended iptv-only{$this->parentPortNumber}\n";
                    $add .= "deny any any\n";
                    $add .= "exit\n";
                    $add .= "interface ethernet {$this->parentPortName}\n";
                    $add .= "shutdown\n";
                    $add .= "description {$this->desc}\n";
                    $add .= "ip igmp filter 8\n";
                    $add .= "port security\n";
                    $add .= "rate-limit input 890000\n";
                    $add .= "rate-limit output 840000\n";
                    $add .= "switchport allowed vlan add {$this->vlanId} untagged\n";
                    $add .= "switchport ingress-filtering\n";
                    $add .= "switchport mode access\n";
                    $add .= "switchport native vlan {$this->vlanId}\n";
                    $add .= "switchport allowed vlan remove 1\n";
                    $add .= "spanning-tree spanning-disabled\n";
                    $add .= "no spanning-tree bpdu-guard\n";
                    $add .= "queue mode strict\n";
                    $add .= "ip access-group iptv-only{$this->parentPortNumber} in\n";
                    $add .= "service-policy input iptv-only-x\n";
                    $add .= "ip dhcp snooping max-number 1\n";
                    $add .= "ip igmp query-drop\n";
                    $add .= "ip multicast-data-drop\n";
                    $add .= "loopback-detection\n";
                    $add .= "discard cdp\n";
                    $add .= "discard pvs\n";
                    $add .= "no shutdown\n";
                    $add .= "end\n";
                    $add .= "cop r s\n";
                }
            }
        } elseif ($this->typeId == GatewayVoip::TYPE) {
            $add = " ";
        } elseif ($this->typeId == Camera::TYPE) {
            $add = " ";
        } elseif ($this->typeId == Virtual::TYPE) {
            $add = "mac-address-table static {$this->mac} interface ethernet {$this->parentPortName}  vlan {$this->vlanId}  permanent\n";
            $add .= "access-list IP extended iptv-user{$this->parentPortNumber}\n";
            $add .= "deny TCP any any destination-port 25\n";
            $add .= "permit host {$this->ip} any\n";
            $add .= "deny any any\n";
            $add .= "exit\n";
            $add .= "interface ethernet {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "description {$this->desc}\n";
            $add .= "ip igmp filter 8\n";
            $add .= "port security\n";
            $add .= "rate-limit input 890000\n";
            $add .= "rate-limit output 840000\n";
            $add .= "switchport allowed vlan add {$this->vlanId} untagged\n";
            $add .= "switchport ingress-filtering\n";
            $add .= "switchport mode access\n";
            $add .= "switchport native vlan {$this->vlanId}\n";
            $add .= "switchport allowed vlan remove 1\n";
            $add .= "spanning-tree spanning-disabled\n";
            $add .= "queue mode strict\n";
            $add .= "ip access-group iptv-user{$this->parentPortNumber} in\n";
            $add .= "service-policy input iptv-user-x\n";
            $add .= "ip dhcp snooping max-number 1\n";
            $add .= "ip igmp query-drop\n";
            $add .= "ip multicast-data-drop\n";
            $add .= "loopback-detection\n";
            $add .= "discard cdp\n";
            $add .= "discard pvs\n";
            $add .= "no shutdown\n";
            $add .= "end\n";
            $add .= "cop r s\n";
        } elseif ($this->typeId == Ups::TYPE) {
            $add = " ";
        }
    return $add;
    }

    function drop($auto) {
        $drop = ' ';        
        if ($this->typeId == Host::TYPE) {
            $drop = "no mac-address-table static {$this->mac} vlan {$this->vlanId}\n";
            $drop .= "interface ethernet {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "ip igmp filter 7\n";
            $drop .= "no port security\n";
            $drop .= "rate-limit input 1000000\n";
            $drop .= "no rate-limit input\n";
            $drop .= "rate-limit output 1000000\n";
            $drop .= "no rate-limit output\n";
            $drop .= "switchport allowed vlan add 555 untagged\n";
            $drop .= "switchport ingress-filtering\n";
            $drop .= "switchport mode access\n";
            $drop .= "switchport native vlan 555\n";
            $drop .= "switchport allowed vlan remove 1\n";
            $drop .= "spanning-tree spanning-disabled\n";
            $drop .= "queue mode strict\n";
            $drop .= "no ip access-group iptv-user{$this->parentPortNumber} in\n";
            $drop .= "no ip access-group iptv-user-smtp{$this->parentPortNumber} in\n";
            $drop .= "no ip access-group internet-user{$this->parentPortNumber} in\n";
            $drop .= "no ip access-group internet-user-smtp{$this->parentPortNumber} in\n";
            $drop .= "no ip access-group iptv-only{$this->parentPortNumber} in\n";
            $drop .= "no service-policy input iptv-user-x\n";
            $drop .= "no service-policy input internet-user-x\n";
            $drop .= "no service-policy input iptv-only-x\n";
            $drop .= "no ip dhcp snooping max-number\n";
            $drop .= "no ip source-guard\n"; 
            $drop .= "ip source-guard mode acl\n";
            $drop .= "ip igmp query-drop\n"; 
            $drop .= "ip multicast-data-drop\n";
            $drop .= "loopback-detection\n";
            $drop .= "discard cdp\n";
            $drop .= "discard pvs\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "no access-list IP extended iptv-user{$this->parentPortNumber}\n";
            $drop .= "no access-list IP extended iptv-user-smtp{$this->parentPortNumber}\n";
            $drop .= "no access-list IP extended internet-user{$this->parentPortNumber}\n";
            $drop .= "no access-list IP extended internet-user-smtp{$this->parentPortNumber}\n";
            $drop .= "no access-list IP extended iptv-only{$this->parentPortNumber}\n";
            $drop .= "exit\n";
            $drop .= "clear ip dhcp snooping binding {$this->mac} {$this->ip}\n";
            $drop .= "cop r s\n";
        } elseif ($this->typeId == GatewayVoip::TYPE) {
            $drop = " ";   
        } elseif ($this->typeId == Camera::TYPE) {
            $drop = " ";    
        } elseif ($this->typeId == Virtual::TYPE) {
            $drop = "no mac-address-table static {$this->mac} vlan {$this->vlanId}\n";
            $drop .= "interface ethernet {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "ip igmp filter 7\n";
            $drop .= "no port security\n";
            $drop .= "rate-limit input 1000000\n";
            $drop .= "no rate-limit input\n";
            $drop .= "rate-limit output 1000000\n";
            $drop .= "no rate-limit output\n";
            $drop .= "switchport allowed vlan add 555 untagged\n";
            $drop .= "switchport ingress-filtering\n";
            $drop .= "switchport mode access\n";
            $drop .= "switchport native vlan 555\n";
            $drop .= "switchport allowed vlan remove 1\n";
            $drop .= "spanning-tree spanning-disabled\n";
            $drop .= "queue mode strict\n";
            $drop .= "no ip access-group iptv-user{$this->parentPortNumber} in\n";
            $drop .= "no ip access-group iptv-user-smtp{$this->parentPortNumber} in\n";
            $drop .= "no ip access-group internet-user{$this->parentPortNumber} in\n";
            $drop .= "no ip access-group internet-user-smtp{$this->parentPortNumber} in\n";
            $drop .= "no ip access-group iptv-only{$this->parentPortNumber} in\n";
            $drop .= "no service-policy input iptv-user-x\n";
            $drop .= "no service-policy input internet-user-x\n";
            $drop .= "no service-policy input iptv-only-x\n";
            $drop .= "no ip dhcp snooping max-number\n";
            $drop .= "no ip source-guard\n";
            $drop .= "ip source-guard mode acl\n";
            $drop .= "ip igmp query-drop\n";
            $drop .= "ip multicast-data-drop\n";
            $drop .= "loopback-detection\n";
            $drop .= "discard cdp\n";
            $drop .= "discard pvs\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "no access-list IP extended iptv-user{$this->parentPortNumber}\n";
            $drop .= "no access-list IP extended iptv-user-smtp{$this->parentPortNumber}\n";
            $drop .= "no access-list IP extended internet-user{$this->parentPortNumber}\n";
            $drop .= "no access-list IP extended internet-user-smtp{$this->parentPortNumber}\n";
            $drop .= "no access-list IP extended iptv-only{$this->parentPortNumber}\n";
            $drop .= "exit\n";
            $drop .= "clear ip dhcp snooping binding {$this->mac} {$this->ip}\n";
            $drop .= "cop r s\n";
        }
        
        return $drop;
    }
    
    function changeMac($newMac) {
        
        $newMac = preg_replace('/^([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/', '$1-$2-$3-$4-$5-$6', str_replace([':', '.', '-'], '', $newMac));
        
        $change = "no mac-address-table static {$this->mac} vlan {$this->vlanId}\n";
        $change .= "mac-address-table static {$newMac} interface ethernet {$this->parentPortName} vlan {$this->vlanId} permanent\n";
        $change .= "exit\n";
        $change .= "clear ip dhcp snooping binding {$this->mac} {$this->ip}\n";
        $change .= "conf\n";
        $change .= "int ethernet {$this->parentPortName}\n";
        $change .= "port security\n";
        $change .= "no shutdown\n";
        $change .= "end\n";
        $change .= "cop r s\n";
    
        return $change;
    }
}