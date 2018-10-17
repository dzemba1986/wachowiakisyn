<?php

namespace common\models\seu\devices\query;

class OpticalTransmitterQuery extends DeviceQuery {
    
	protected function getColumns() {
	    
	    return parent::getColumns() . ', input_level, output_power, insertion_loss';
	}
}
?>
