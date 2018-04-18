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
    
    function __construct(Device $device, Device $parentDevice) {
        
        $this->device = $device;
        $parentPortIndex = $device->links[0]->parent_port;
        $this->parentPortNumber = $parentPortIndex + 1;
        $this->parentPortName = $parentDevice->model->port[$parentPortIndex];
        $this->vlanId = $device->ips[0]->subnet->vlan->id;
        $this->ip = $device->ips[0]->ip;
        $this->desc = $device->getMixName(false);
    }
    abstract function add();
    abstract function drop();
//     abstract function changeMac($newMac); 
}