<?php

namespace common\models\seu\devices\query;

class RadioQuery extends DeviceQuery {
    
    protected function getColumns() {
        
        return parent::getColumns() . ', monitoring, dhcp';
    }
}
?>
