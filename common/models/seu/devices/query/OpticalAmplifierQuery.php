<?php

namespace common\models\seu\devices\query;

class OpticalAmplifierQuery extends DeviceQuery {
    
	protected function getColumns() {
	    
	    return parent::getColumns() . ', input_power, output_power, insertion_loss';
	}
}
?>
