<?php

namespace common\models\seu\devices\query;

class OpticalSplitterQuery extends DeviceQuery {
    
	protected function getColumns() {
	    
	    return parent::getColumns() . ', insertion_loss';
	}
}
?>
