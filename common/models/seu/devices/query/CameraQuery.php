<?php

namespace common\models\seu\devices\query;

class CameraQuery extends DeviceQuery {
    
	protected function getColumns() {
	    
	    return parent::getColumns() . ', alias, dhcp, monitoring, geolocation';
	}
}
?>
