<?php

namespace common\models\soa;


/**
 * @property integer $host_id
 * @property string $mac
 * @property common\models\seu\devices\ $host
 */

class Phone extends Connection {

    const TYPE = 2;
    const PACKAGE = [1 => 'T', 2 => 'TP'];
    
	public function rules() {
	    
		return [
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
		];
	}
}