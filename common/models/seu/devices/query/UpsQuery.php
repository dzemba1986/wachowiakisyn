<?php

namespace common\models\seu\devices\query;

class UpsQuery extends DeviceQuery {
    
    protected function getColumns() {
        
        return parent::getColumns() . ', monitoring, dhcp, geolocation';
    }
}
?>
