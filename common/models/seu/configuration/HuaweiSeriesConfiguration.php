<?php

namespace common\models\seu\configuration;

use common\models\seu\devices\Camera;
use common\models\seu\devices\GatewayVoip;
use common\models\seu\devices\Host;
use common\models\seu\devices\Ups;
use common\models\seu\devices\Virtual;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;

class HuaweiSeriesConfiguration extends Configuration {
    
    function __construct($device) {
        
        parent::__construct($device);
        $this->mac = MacaddressValidator::formatValue($device->mac, 'hw');
    }
    
    function add() {
        $add = ' ';
        if ($this->typeId == Host::TYPE) {
            if (strpos($this->parentIp, '172.') === 0) {
                $add = "interface {$this->parentPortName}\n";
                $add .= "shutdown\n";
                $add .= "description {$this->desc}\n";
                $add .= "port default vlan {$this->vlanId}\n";
                $add .= "loopback-detect enable\n";
                $add .= "traffic-filter inbound acl name net-user\n";
                $add .= "undo lldp enable\n";
                $add .= "undo port-security enable\n";
                $add .= "port-security enable\n";
                $add .= "port-security mac-address {$this->mac} vlan {$this->vlanId}\n";
                $add .= "dhcp snooping check dhcp-chaddr enable\n";
                $add .= "dhcp snooping max-user-number 1\n";
                $add .= "undo shutdown\n";
                $add .= "quit\n";
                $add .= "quit\n";
                $add .= "save\n";
                $add .= "Y\n";
            } else {
                if ($this->connectionsCount == 2) {
                    $add = " ";
                } elseif ($this->connectionsCount == 1) {
                    if ($this->connectionType == 1) {
                        $add = " ";
                    } elseif ($this->connectionType == 3) {
                        $add = " ";
                    }
                }
            }
        } elseif ($this->typeId == GatewayVoip::TYPE) {
	        	$add = "acl name voip" . sprintf("%02d", $this->parentPortNumber) . " 31" . sprintf("%02d", $this->parentPortNumber) . "\n";
            $add .= "rule 5 deny udp destination-port eq bootpc\n";
            $add .= "rule 10 permit ip source {$this->ip} 0 destination 213.5.208.0 0.0.0.63\n";
            $add .= "rule 15 permit ip source {$this->ip} 0 destination 213.5.208.128 0.0.0.63\n";
            $add .= "rule 20 permit ip source {$this->ip} 0 destination 10.111.0.0 0.0.255.255\n";
            $add .= "rule 25 permit udp source 0.0.0.0 0 source-port eq bootpc destination-port eq bootps\n";
            $add .= "rule 30 deny ip\n";
            $add .= "quit\n";
            $add .= "interface {$this->parentPortName}\n";
            $add .= "shutdown\n";
            $add .= "description {$this->desc}\n";
            $add .= "port default vlan {$this->vlanId}\n";
            $add .= "loopback-detect enable\n";
            $add .= "traffic-filter inbound acl name voip" . sprintf("%02d", $this->parentPortNumber) . "\n";
            $add .= "undo lldp enable\n";
            $add .= "undo port-security enable\n";
            $add .= "port-security enable\n";
            $add .= "port-security mac-address {$this->mac} vlan {$this->vlanId}\n";
            $add .= "undo shutdown\n";
            $add .= "quit\n";
            $add .= "quit\n";
            $add .= "save\n";
            $add .= "Y\n";
        } elseif ($this->typeId == Camera::TYPE) {
	        	$add = "acl name cam" . sprintf("%02d", $this->parentPortNumber) . " 32" . sprintf("%02d", $this->parentPortNumber) . "\n";
	        	$add .= "rule 5 deny udp destination-port eq bootpc\n";
	        	$add .= "rule 10 permit ip source {$this->ip} 0 destination 213.5.208.128 0.0.0.63\n";
	        	$add .= "rule 15 permit ip source {$this->ip} 0 destination 192.168.5.0 0.0.0.255\n";
	        	$add .= "rule 20 permit ip source {$this->ip} 0 destination 10.111.0.0 0.0.255.255\n";
	        	$add .= "rule 25 permit udp source 0.0.0.0 0 source-port eq bootpc destination-port eq bootps\n";
	        	$add .= "rule 30 deny ip\n";
	        	$add .= "quit\n";
	        	$add .= "interface {$this->parentPortName}\n";
	        	$add .= "shutdown\n";
	        	$add .= "description {$this->desc}\n";
	        	$add .= "port default vlan {$this->vlanId}\n";
	        	$add .= "loopback-detect enable\n";
	        	$add .= "traffic-filter inbound acl name cam" . sprintf("%02d", $this->parentPortNumber) . "\n";
	        	$add .= "undo lldp enable\n";
	        	$add .= "undo port-security enable\n";
	        	$add .= "port-security enable\n";
	        	$add .= "port-security mac-address {$this->mac} vlan {$this->vlanId}\n";
	        	$add .= "undo shutdown\n";
	        	$add .= "quit\n";
	        	$add .= "quit\n";
	        	$add .= "save\n";
	        	$add .= "Y\n";
        } elseif ($this->typeId == Ups::TYPE) {
            $add = " ";
        } elseif ($this->typeId == Virtual::TYPE) {
            $add = " ";
        }

    return $add;
    }

    function drop($auto) {
        $drop = '';
        if ($this->typeId == Host::TYPE) {
            if (strpos($this->parentIp, '172.') === 0) {
                $drop .= "interface {$this->parentPortName}\n";
                $drop .= "shutdown\n";
                $drop .= "port default vlan 555\n";
                $drop .= "undo port-security enable\n";
                $drop .= "undo traffic-filter inbound acl name net-user\n";
                $drop .= "undo dhcp snooping check dhcp-chaddr enable\n";
                $drop .= "undo dhcp snooping max-user-number\n";
                $drop .= "loopback-detect enable\n";
                $drop .= "undo lldp enable\n";
                $drop .= "undo shutdown\n";
                $drop .= "quit\n";
                $drop .= "quit\n";
                $drop .= "reset dhcp snooping user-bind interface {$this->parentPortName}\n";
                $drop .= "save\n";
                $drop .= "Y\n";
            } else {
                $drop = " ";
            }
            
        } elseif ($this->typeId == GatewayVoip::TYPE) {
            $drop = "interface {$this->parentPortName}\n";
            $drop .= "shutdown\n";
            $drop .= "port default vlan 555\n";
            $drop .= "undo traffic-filter inbound acl name voip" . sprintf("%02d", $this->parentPortNumber) . "\n";
            $drop .= "undo port-security enable\n";
            $drop .= "undo description\n";
            $drop .= "undo shutdown\n";
            $drop .= "quit\n";
            $drop .= "undo acl name voip{$this->parentPortNumber}\n";
            $drop .= "quit\n";
            $drop .= "save\n";
            $drop .= "Y\n";
        } elseif ($this->typeId == Camera::TYPE) {
	        	$drop = "interface {$this->parentPortName}\n";
	        	$drop .= "shutdown\n";
	        	$drop .= "port default vlan 555\n";
	        	$drop .= "undo traffic-filter inbound acl name cam" . sprintf("%02d", $this->parentPortNumber) . "\n";
	        	$drop .= "undo port-security enable\n";
	        	$drop .= "undo description\n";
	        	$drop .= "undo shutdown\n";
	        	$drop .= "quit\n";
	        	$drop .= "undo acl name cam{$this->parentPortNumber}\n";
	        	$drop .= "quit\n";
	        	$drop .= "save\n";
	        	$drop .= "Y\n";
        } elseif ($this->typeId == Ups::TYPE) {
            $drop = " ";
        } elseif ($this->typeId == Virtual::TYPE) {
            $drop = " ";
        }
    return $drop;
    }
    
    function changeMac($newMac) {
        $newMac = preg_replace('/^([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})([A-Fa-f0-9]{4})$/', '$1-$2-$3', str_replace([':', '.', '-'], '', $newMac));
        
        $change = "interface {$this->parentPortName}\n";
        $change .= "shutdown\n";
        $change .= "undo port-security enable\n";
        $change .= "port-security enable\n";
        $change .= "port-security mac-address {$newMac} vlan {$this->vlanId}\n";
        $change .= "quit\n";
        $change .= "quit\n";
        $change .= "reset dhcp snooping user-bind interface {$this->parentPortName}\n";
        $change .= "system-view\n";
        $change .= "interface {$this->parentPortName}\n";
        $change .= "undo shutdown\n";
        $change .= "quit\n";
        $change .= "quit\n";
        $change .= "save\n";
        $change .= "Y\n";

    return $change;
    }
}