<?php

namespace common\models\seu\devices\query;

class HostEthernetQuery extends HostQuery {
    
    protected function getColumns() {
        
        return parent::getColumns() . ', mac, smtp, dhcp';
    }
    
    public function prepare($builder) {
        
        if ($this->technic !== NULL) {
            $this->andWhere(['technic' => $this->technic]);
        }
        
        return parent::prepare($builder);
    }
}
?>
