<?php

namespace common\models\seu\devices\query;

class HostEthernetQuery extends HostQuery {
    
    protected function getColumns() {
        
        return parent::getColumns() . ', mac, smtp, dhcp';
    }
}
?>
