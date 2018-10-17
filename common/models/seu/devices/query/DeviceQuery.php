<?php

namespace common\models\seu\devices\query;

use yii\db\ActiveQuery;

class DeviceQuery extends ActiveQuery {
    
	public $type_id;
    
	protected function getColumns() {
	    
	    return 'id, status, name, desc, mac, serial, model_id, manufacturer_id, address_id, type_id, proper_name';
	}
	
	public function prepare($builder) {
	    
		if ($this->type_id !== NULL) {
		    $this->select($this->columns)->andWhere(['device.type_id' => $this->type_id]);
		}
		
		return parent::prepare($builder);
	}
}
?>
