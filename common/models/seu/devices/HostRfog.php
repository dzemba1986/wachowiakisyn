<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\HostRfogQuery;

/**
 * @property integer $technic
 * @property backend\models\Connection[] $connections
 */

class HostRfog extends Host {
    
	const TECHNIC = 2;
	
	public function init() {
		
	    $this->type_id = parent::TYPE;
	    $this->technic = self::TECHNIC;
		parent::init();
	}
	
	public static function find() {
	    
	    return new HostRfogQuery(get_called_class(), ['type_id' => parent::TYPE, 'technic' => self::TECHNIC]);
	}
	
	public function beforeSave($insert) {
	    
	    $this->type_id = parent::TYPE;
	    $this->technic = self::TECHNIC;
	    return parent::beforeSave($insert);
	}
	
	public static function getCanSwitchToParentTypeIds() {
	    
	    return [OpticalSplitter::TYPE];
	}
}