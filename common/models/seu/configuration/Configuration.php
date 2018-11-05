<?php

namespace common\models\seu\configuration;

use common\models\seu\devices\Device;

abstract class Configuration {
    
    protected $device;
    protected $parentPortNumber;
    protected $parentPortName;
    protected $vlanId;
    protected $mac;
    protected $ip;
    protected $desc;
    
    function __construct(Device $device) {
        
        $this->parentPortNumber = $device->parent->portNumber;
        $this->parentPortName = $device->parent->portName;
        $this->desc = $device->getMixName(false);
        $this->vlanId = $device->vlansToIps[0]['vlan'];
        $this->ip = $device->vlansToIps[0]['ip'];
        $this->typeId = $device->type_id;
        $this->smtp = $device->smtp;
        $this->parentIp = $device->parent->firstIp;
        $this->device = $device;
    }
    abstract function add();
    abstract function drop($auto);
//     abstract function changeMac($newMac); 
}