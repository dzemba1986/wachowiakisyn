<?php

namespace common\models\seu\devices\traits;

use common\models\seu\configuration\ECSeriesConfiguration;
use common\models\seu\configuration\GSSeriesConfiguration;
use common\models\seu\configuration\HuaweiSeriesConfiguration;
use common\models\seu\configuration\XSeriesConfiguration;

trait Config {
    
    private $conf = NULL;
    
    public function configAdd() {

        if ($this->getHasIps()) {
            if ($this->configParent->configType == 1) $this->conf = new GSSeriesConfiguration($this);
            elseif ($this->configParent->configType == 2) $this->conf = new XSeriesConfiguration($this);
            elseif ($this->configParent->configType == 5) $this->conf = new ECSeriesConfiguration($this);
            elseif ($this->configParent->configType == 8) $this->conf = new HuaweiSeriesConfiguration($this);
            else return ' ';
        } else return ' ';
        
        return $this->conf->add();
    }
    
    public function configDrop($auto = FALSE) {
        
        if ($this->getHasIps()) {
            if ($this->configParent->configType == 1) $this->conf = new GSSeriesConfiguration($this);
            elseif ($this->configParent->configType == 2) $this->conf = new XSeriesConfiguration($this);
            elseif ($this->configParent->configType == 5) $this->conf = new ECSeriesConfiguration($this);
            elseif ($this->configParent->configType == 8) $this->conf = new HuaweiSeriesConfiguration($this);
            else return ' ';
        } else return ' ';
        
        return $this->conf->drop($auto);
    }
    
    public function configChangeMac($newMac) {

        if ($this->getHasIps()) {
            if ($this->configParent->configType == 1) $this->conf = new GSSeriesConfiguration($this);
            elseif ($this->configParent->configType == 2) $this->conf = new XSeriesConfiguration($this);
            elseif ($this->configParent->configType == 5) $this->conf = new ECSeriesConfiguration($this);
            elseif ($this->configParent->configType == 8) $this->conf = new HuaweiSeriesConfiguration($this);
            else return ' ';
        } else return ' ';
        
        return $this->conf->changeMac($newMac);
    }
}
?>