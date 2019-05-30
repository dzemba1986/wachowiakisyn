<?php

namespace common\models\soa;


/**
 * @property integer $host_id
 * @property string $mac
 * @property common\models\seu\devices\ $host
 */

class Tv extends Connection {

    const TYPE = 3;
    const PACKAGE = [4 => 'DVB-C', 5 => 'IPTV', 6 => 'RFoG'];
    
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