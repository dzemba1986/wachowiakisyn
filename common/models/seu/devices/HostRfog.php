<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\HostRfogQuery;
use yii\helpers\ArrayHelper;

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
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'input_power',
	        ]
        );
	}
	
	public function fields() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'input_power',
	        ]
        );
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'input_power' => 'Moc IN',
	        ]
	        );
	}
	
	public function rules() {
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['input_power', 'string'],
	            ['input_power', 'default', 'value' => null],
	            
	            [['input_power'], 'safe'],
            ]
        );
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['input_power']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['input_power']);
	    
	    return $scenarios;
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