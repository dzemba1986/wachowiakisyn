<?php

namespace common\models\seu\devices\query;

class VirtualQuery extends DeviceQuery {
    
    protected function getColumns() {
        
        return parent::getColumns() . ', dhcp';
    }
}
?>
