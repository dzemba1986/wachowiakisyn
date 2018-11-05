<?php

namespace common\models\seu\devices\query;

class HostQuery extends DeviceQuery {
    
    public $technic;
    
	protected function getColumns() {
	    
	    return 'id, status, name, desc, address_id, type_id, proper_name, technic';
	}
	
	public function prepare($builder) {
	    
	    if ($this->technic !== NULL) {
	        $this->andWhere(['technic' => $this->technic]);
	    }
	    
	    return parent::prepare($builder);
	}
}
?>
