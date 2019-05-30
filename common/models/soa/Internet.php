<?php

namespace common\models\soa;

use common\models\seu\devices\Host;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;

/**
 * @property integer $host_id
 * @property string $mac
 * @property common\models\seu\devices\ $host
 */

class Internet extends Connection {
    
    const TYPE = 1;
    const PACKAGE = [3 => 'IS'];
    
	public function rules() {
	    
		return [
			['mac', 'default', 'value' => null],
			['mac', MacaddressValidator::class, 'message' => 'Zły format'],
			['mac', 'trim', 'skipOnEmpty' => true],
		];
	}
	
	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = [];
		$scenarios[self::SCENARIO_UPDATE] = [];
		$scenarios[self::SCENARIO_CLOSE] = [];
		 
		return $scenarios;
	}

	public function attributeLabels() {
	    
		return [
		    'mac' => 'MAC'
		];
	}
	
    public function getHost() {
        
        return $this->hasOne(Host::class, ['id' => 'host_id']);
    }
    
    public function canConfigure() : bool {
        //kabel musi być gdyż po konfiguracji szczurek nie ma już na liście odpowiedniego portu do wyboru 
        return !$this->nocontract && is_null($this->close_date) && is_null($this->host_id) && $this->type_id <> 2 && $this->wire > 0 && $this->exec_date <= date('Y-m-d')? true : false;
    }
}