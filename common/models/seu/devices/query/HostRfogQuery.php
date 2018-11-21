<?php

namespace common\models\seu\devices\query;

class HostRfogQuery extends HostQuery {
    
    protected function getColumns() {
        
        return parent::getColumns() . ', input_power';
    }
}
?>
