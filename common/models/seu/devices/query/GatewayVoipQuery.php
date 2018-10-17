<?php

namespace common\models\seu\devices\query;

class GatewayVoipQuery extends DeviceQuery {
    
    protected function getColumns() {
        
        return parent::getColumns() . ', monitoring, geolocation';
    }
}
?>
