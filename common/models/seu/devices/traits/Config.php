<?php

namespace common\models\seu\devices\traits;

use common\models\seu\configuration\ECSeriesConfiguration;
use common\models\seu\configuration\GSSeriesConfiguration;
use common\models\seu\configuration\XSeriesConfiguration;

trait Config {
    
    private $conf = NULL;
    
    public function configAdd() {

        if ($this->getHasIps()) {
            if ($this->getParent()->configType == 1) $this->conf = new GSSeriesConfiguration($this);
            elseif ($this->getParent()->configType == 2) $this->conf = new XSeriesConfiguration($this);
            elseif ($this->getParent()->configType == 5) $this->conf = new ECSeriesConfiguration($this);
            else return ' ';
        } else return ' ';
        
        return $this->conf->add();
    }
    
    public function configDrop($auto = FALSE) {
        
        if ($this->getHasIps()) {
            if ($this->getParent()->configType == 1) $this->conf = new GSSeriesConfiguration($this);
            elseif ($this->getParent()->configType == 2) $this->conf = new XSeriesConfiguration($this);
            elseif ($this->getParent()->configType == 5) $this->conf = new ECSeriesConfiguration($this);
            else return ' ';
        } else return ' ';
        
        return $this->conf->drop($auto);
    }
    
    public function configChangeMac($newMac) {

        if ($this->getHasIps()) {
            if ($this->getParent()->configType == 1) $this->conf = new GSSeriesConfiguration($this);
            elseif ($this->getParent()->configType == 2) $this->conf = new XSeriesConfiguration($this);
            elseif ($this->getParent()->configType == 5) $this->conf = new ECSeriesConfiguration($this);
            else return ' ';
        } else return ' ';
        
        return $this->conf->changeMac($newMac);
    }
}
?>