<?php

namespace backend\models\configuration;

use backend\models\Device;

abstract class Configuration {
    
    protected $device;
    protected $parentPortNumber;
    protected $parentPortName;
    protected $vlanId;
    protected $mac;
    protected $ip;
    protected $desc;
    
    function __construct(Device $device) {
        
        $this->device = $device;
        $this->parentPortNumber = $device->parentPortIndex;
        $this->parentPortName = $device->parentPortName;
        $this->vlanId = $device->ips[0]->subnet->vlan->id;
        $this->ip = $device->ips[0]->ip;
        $this->desc = $device->getMixName(false);
    }
    abstract function add();
    abstract function drop($auto);
//     abstract function changeMac($newMac); 
}