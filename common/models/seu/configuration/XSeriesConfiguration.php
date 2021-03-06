<?php

namespace common\models\seu\configuration;

use common\models\seu\devices\Camera;
use common\models\seu\devices\GatewayVoip;
use common\models\seu\devices\Host;
use common\models\seu\devices\Ups;
use common\models\seu\devices\Virtual;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;

class XSeriesConfiguration extends Configuration {
    
    function __construct($device) {
        
        parent::__construct($device);
        $this->mac = MacaddressValidator::formatValue($device->mac, 'x');
    }
    
    function add() {
        $add = ' ';
        if ($this->typeId == Host::TYPE) {
            if (strpos($this->parentIp, '172.') === 0) {
                $add = "interface {$this->parentPortName}\n";
                $add .= "shutdown\n";
                $add .= "no switchport port-security\n";
                $add .= "switchport port-security violation protect\n";
                $add .= "switchport port-security maximum 0\n";
                $add .= "switchport port-security\n";
                $add .= "description {$this->desc}\n";
                $this->smtp ? $add .= "access-group anyuser-smtp\n" : $add .= "access-group anyuser\n";
                $add .= "switchport access vlan {$this->vlanId}\n";
                $add .= "spanning-tree portfast\n";
                $add .= "spanning-tree portfast bpdu-guard enable\n";
                $add .= "no shutdown\n";
                $add .= "exit\n";
                $add .= "mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
                $add .= "exit\n";
                $add .= "wr\n";
            } else {
                if ($this->connectionsCount == 2) {
                    $add = "interface {$this->parentPortName}\n";
                    $add .= "shutdown\n";
                    $add .= "no switchport port-security\n";
                    $add .= "switchport port-security violation protect\n";
                    $add .= "switchport port-security maximum 0\n";
                    $add .= "switchport port-security\n";
                    $add .= "spanning-tree portfast\n";
                    $add .= "spanning-tree portfast bpdu-guard enable\n";
                    $add .= "description {$this->desc}\n";
                    $add .= "service-policy input iptv-user-1G\n";
                    $this->smtp ? $add .= "access-group iptv-user-smtp\n" : $add .= "access-group iptv-user\n";
                    $add .= "no ip igmp trusted all\n";
                    $add .= "ip igmp trusted report\n";
                    $add .= "switchport access vlan {$this->vlanId}\n";
                    $add .= "no shutdown\n";
                    $add .= "exit\n";
                    $add .= "mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
                    $add .= "exit\n";
                    $add .= "wr\n";
                } elseif ($this->connectionsCount == 1) {
                    if ($this->connectionType == 1) {
                        $add = "interface {$this->parentPortName}\n";
                        $add .= "shutdown\n";
                        $add .= "no switchport port-security\n";
                        $add .= "switchport port-security violation protect\n";
                        $add .= "switchport port-security maximum 0\n";
                        $add .= "switchport port-security\n";
                        $add .= "spanning-tree portfast\n";
                        $add .= "spanning-tree portfast bpdu-guard enable\n";
                        $add .= "description {$this->desc}\n";
                        $add .= "service-policy input internet-user-1G\n";
                        $this->smtp ? $add .= "access-group internet-user-smtp\n" : $add .= "access-group internet-user\n";
                        $add .= "no ip igmp trusted all\n";
                        $add .= "switchport access vlan {$this->vlanId}\n";
                        $add .= "no shutdown\n";
                        $add .= "exit\n";
                        $add .= "mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
                        $add .= "exit\n";
                        $add .= "wr\n";
                    } elseif ($this->connectionType == 3) {
                        $add = "interface {$this->parentPortName}\n";
                        $add .= "shutdown\n";
                        $add .= "no switchport port-security\n";
                        $add .= "switchport port-security violation protect\n";
                        $add .= "switchport port-security maximum 0\n";
                        $add .= "switchport port-security\n";
                        $add .= "spanning-tree portfast\n";
                        $add .= "spanning-tree portfast bpdu-guard enable\n";
                        $add .= "description {$this->desc}\n";
                        $add .= "service-policy input iptv-only-1G\n";
                        $add .= "access-group iptv-only\n";
                        $add .= "no ip igmp trusted all\n";
                        $add .= "ip igmp trusted report\n";
                        $add .= "switchport access vlan {$this->vlanId}\n";
                        $add .= "no shutdown\n";
                        $add .= "exit\n";
                        $add .= "mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
                        $add .= "exit\n";
                        $add .= "wr\n";
                    }
                }
            }
        } elseif ($this->typeId == GatewayVoip::TYPE) {
            $add = "interface {$this->parentPortName}\n";
            $add .= "no access-group voip{$this->parentPortNumber}\n";
            $add .= "exit\n";
            $add .= "no access-list hardware voip{$this->parentPortNumber}\n";
            $add .= "access-list hardware voip{$this->parentPortNumber}\n";
            $add .= "deny udp any any eq 68\n";
            $add .= "permit ip {$this->ip} 0.0.0.0 213.5.208.0 0.0.0.63\n";
            $add .= "permit ip {$this->ip} 0.0.0.0 213.5.208.128 0.0.0.63\n";
            $add .= "permit ip {$this->ip} 0.0.0.0 10.111.0.0 0.0.255.255\n";
            $add .= "permit udp 0.0.0.0 0.0.0.0 eq 68 any eq 67\n";
            $add .= "deny ip any any\n";
            $add .= "exit\n";
            $add .= "interface {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "description {$this->desc}\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "access-group voip{$this->parentPortNumber}\n";
            $add .= "switchport port-security violation protect\n";
            $add .= "switchport port-security maximum 0\n";
            $add .= "switchport port-security\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree portfast bpdu-guard enable\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
            $add .= "exit\n";
            $add .= "wr\n";
        } elseif ($this->typeId == Camera::TYPE) {
            $add = "interface {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "description {$this->desc}\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "access-group camera\n";
            $add .= "switchport port-security violation protect\n";
            $add .= "switchport port-security maximum 0\n";
            $add .= "switchport port-security\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree portfast bpdu-guard enable\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
            $add .= "exit\n";
            $add .= "wr\n";
        } elseif ($this->typeId == Ups::TYPE) {
            $add = "interface {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "description {$this->desc}\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "access-group ups\n";
            $add .= "switchport port-security violation protect\n";
            $add .= "switchport port-security maximum 0\n";
            $add .= "switchport port-security\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree portfast bpdu-guard enable\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
            $add .= "exit\n";
            $add .= "wr\n";
        } elseif ($this->typeId == Virtual::TYPE) {
            $add = "interface {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "no switchport port-security\n";
            $add .= "switchport port-security violation protect\n";
            $add .= "switchport port-security maximum 0\n";
            $add .= "switchport port-security\n";
            $add .= "spanning-tree portfast\n";
            $add .= "spanning-tree portfast bpdu-guard enable\n";
            $add .= "description {$this->desc}\n";
            $add .= "service-policy input iptv-user-1G\n";
            $add .= "access-group iptv-user\n";
            $add .= "no ip igmp trusted all\n";
            $add .= "ip igmp trusted report\n";
            $add .= "switchport access vlan {$this->vlanId}\n";
            $add .= "no shutdown\n";
            $add .= "exit\n";
            $add .= "mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
            $add .= "exit\n";
            $add .= "wr\n";
        }

    return $add;
    }

    function drop($auto) {
        $drop = '';
        $user = 'ra-daniel';
        $pass = 'Mustang1986.';
        if ($this->typeId == Host::TYPE) {
            
            if (strpos($this->parentIp, '172.') === 0) {
                if ($auto) $drop .= "en\n";
                if ($auto) $drop .= "conf t\n";
                $drop .= "no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
                $drop .= "interface {$this->parentPortName}\n";
                $drop .= "no switchport port-security\n";
                $this->smtp ? $drop .= "no access-group anyuser-smtp\n" : $drop .= "no access-group anyuser\n";
                $drop .= "switchport access vlan 555\n";
                $drop .= "exit\n";
                $drop .= "do clear ip dhcp snooping binding int {$this->parentPortName}\n";
                $drop .= "exit\n";
                $drop .= "wr\n";
                
                if ($auto) exec("sshpass -p$pass ssh -T -p22222 -o ConnectTimeout=1 -o ConnectionAttempts=1 -o StrictHostKeyChecking=no $user@{$this->parentIp} << DUPA $drop DUPA");
            } else {
                if ($auto) $drop .= "en"; 
                if ($auto) $drop .= "conf t ";
                $drop .= "no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\r\n";
                $drop .= "int {$this->parentPortName}\r\n";
                $drop .= "no switchport port-security\r\n";
                $drop .= "no service-policy input internet-user-1G\r\n";
                $drop .= "no service-policy input iptv-user-1G\r\n";
                $drop .= "no service-policy input iptv-only-1G\r\n";
                $drop .= "no ip igmp trust all\r\n";
                $this->smtp ? $drop .= "no access-group internet-user-smtp\r\n" : $drop .= "no access-group internet-user\r\n";
                $this->smtp ? $drop .= "no access-group iptv-user-smtp\r\n" : $drop .= "no access-group iptv-user\r\n";
                $drop .= "no access-group iptv-only\r\n";
                $drop .= "switchport access vlan 555\r\n";
                $drop .= "exit\r\n";
                $drop .= "do clear ip dhcp snooping binding interface {$this->parentPortName}\r\n";
                $drop .= "exit\r\n";
                $drop .= "wr\r\n";
                
                if ($auto) exec("sshpass -p$pass ssh -T -p22222 -o ConnectTimeout=1 -o ConnectionAttempts=1 -o StrictHostKeyChecking=no $user@{$this->parentIp} << DUPA $drop DUPA");
            }
            
        } elseif ($this->typeId == GatewayVoip::TYPE) {
            $drop = "no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
            $drop .= "interface {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "no access-group voip{$this->parentPortNumber}\n";
            $drop .= "no switchport port-security\n";
            $drop .= "switchport access vlan 555\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "no access-list hardware voip{$this->parentPortNumber}\n";
            $drop .= "exit\n";
            $drop .= "wr\n";
        } elseif ($this->typeId == Camera::TYPE) {
            $drop = "no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
            $drop .= "interface {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "no access-group camera\n";
            $drop .= "no switchport port-security\n";
            $drop .= "switchport access vlan 555\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "exit\n";
            $drop .= "clear ip dhcp snooping binding interface {$this->parentPortName}\n";
            $drop .= "wr\n";
        } elseif ($this->typeId == Ups::TYPE) {
            $drop = "no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
            $drop .= "interface {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "no access-group ups\n";
            $drop .= "no switchport port-security\n";
            $drop .= "switchport access vlan 555\n";
            $drop .= "no shutdown\n";
            $drop .= "exit\n";
            $drop .= "exit\n";
            $drop .= "clear ip dhcp snooping binding interface {$this->parentPortName}\n";
            $drop .= "wr\n";
        } elseif ($this->typeId == Virtual::TYPE) {
            $drop = "no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
            $drop .= "int {$this->parentPortName}\n";
            $drop .= "no switchport port-security\n";
            $drop .= "no service-policy input internet-user-1G\n";
            $drop .= "no service-policy input iptv-user-1G\n";
            $drop .= "no service-policy input iptv-only-1G\n";
            $drop .= "no ip igmp trust all\n";
            $drop .= "no access-group internet-user\n";
            $drop .= "no access-group iptv-user\n";
            $drop .= "no access-group iptv-only\n";
            $drop .= "switchport access vlan 555\n";
            $drop .= "exit\n";
            $drop .= "do clear ip dhcp snooping binding interface {$this->parentPortName}\n";
            $drop .= "exit\n";
            $drop .= "wr\n";
        }
    return $drop;
    }
    
    function changeMac($newMac) {
        $newMac = preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1.$2.$3', str_replace([':', '.', '-'], '', $newMac));
        
        $change = "interface {$this->parentPortName}\n";
        $change .= "shutdown\n";
        $change .= "no switchport port-security\n";
        $change .= "exit\n";
        $change .= "no mac address-table static {$this->mac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
        $change .= "mac address-table static {$newMac} forward interface {$this->parentPortName} vlan {$this->vlanId}\n";
        $change .= "exit\n";
        $change .= "clear ip dhcp snooping binding interface {$this->parentPortName}\n";
        $change .= "configure terminal\n";
        $change .= "interface {$this->parentPortName}\n";
        $change .= "switchport port-security\n";
        $change .= "no shutdown\n";
        $change .= "exit\n";
        $change .= "exit\n";
        $change .= "wr\n";	

    return $change;
    }
}