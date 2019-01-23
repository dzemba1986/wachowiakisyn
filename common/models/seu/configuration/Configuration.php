<?php

namespace common\models\seu\configuration;

use common\models\seu\devices\Device;
use common\models\seu\devices\Host;

abstract class Configuration {
    
    protected $parentPortNumber;
    protected $parentPortName;
    protected $vlanId;
    protected $mac;
    protected $ip;
    protected $desc;
    protected $parentIp;
    protected $smtp;
    protected $connectionsCount;
    protected $connectionType;
    
    function __construct(Device $device) {
        
        $this->parentPortNumber = $device->configParent->portNumber;
        $this->parentPortName = $device->configParent->portName;
        $this->desc = $device->getMixName(false);
        $this->vlanId = $device->vlansToIps[0]['vlan_id'];
        $this->ip = $device->vlansToIps[0]['ip'];
        $this->typeId = $device->type_id;
        $this->parentIp = $device->configParent->firstIp;
        
        if ($this->typeId == Host::TYPE) {
            $this->smtp = $device->smtp;
            $this->connectionsCount = $device->getConnections()->count();
            $this->connectionType = $device->connections[0]->type_id;
        } 
    }
    abstract function add();
    abstract function drop($auto);
//     abstract function changeMac($newMac); 
}