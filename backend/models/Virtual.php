<?php

namespace backend\models;

use yii\helpers\ArrayHelper;

/**
 * @property integer $id
 * @property boolean $status
 * @property string $name
 * @property string $proper_name
 * @property string $desc
 * @property boolean $dhcp
 * @property boolean $smtp
 * @property integer $address_id
 * @property integer $type_id
 * @property integer $mac
 * @property string $serial
 * @property integer $model_id
 * @property integer $manufacturer_id
 */

class Virtual extends Device {
    
	const TYPE = 7;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'dhcp',
	            'smtp'
	        ]
		);
	}
	
	public static function find() {
	    
		return new DeviceQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
		if(!$insert) 
			$this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}
	
	public function rules() {
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['dhcp', 'boolean'],
	            ['dhcp', 'default', 'value' => false],
	            ['dhcp', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['smtp', 'boolean'],
	            ['smtp', 'default', 'value' => false],
	            ['smtp', 'required', 'message' => 'Wartość wymagana'],
	            
	            [['mac', 'dhcp', 'smtp'], 'safe'],
            ]
        );
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac', 'dhcp', 'smtp']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'dhcp', 'smtp']);
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'dhcp' => 'DHCP',
	            'smtp' => 'SMTP',
	        ]
        );
	}
}