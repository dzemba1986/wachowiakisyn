<?php
namespace common\models\seu\devices\interfaces;

interface CanHasIp {
    
    public function getHasIps();
    public function getIps();
    public function getMainIp();
    public function getVlansToIps();
}

