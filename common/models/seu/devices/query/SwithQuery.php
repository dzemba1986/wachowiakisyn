<?php

namespace common\models\seu\devices\query;

class SwithQuery extends DeviceQuery {
    
	protected function getColumns() {
	    
	    return parent::getColumns() . ', distribution, monitoring, geolocation';
	}
}
?>
