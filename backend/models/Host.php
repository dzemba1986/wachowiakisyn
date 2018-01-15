<?php

namespace backend\models;

use yii\helpers\ArrayHelper;

/**
 * @property integer $id
 * @property boolean $status
 * @property string $name
 * @property string $proper_name
 * @property string $desc
 * @property integer $address_id
 * @property integer $type_id
 * @property integer $mac
 * @property boolean $dhcp
 * @property boolean $smtp
 * @property string $start_date
 * @property Address $address
 * @property Type $type
 */

class Host extends Device {
    
	const TYPE = 5;
	
	public function init() {
		
	    $this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'start_date',
	            'dhcp',
	            'smtp'
	        ]
	    );
	}
	
	public static function find() {
		
	    return new DeviceQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
		
	    $this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}

	public function rules() {
		
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['mac', 'required', 'message' => 'Wartość wymagana'],
                
                ['dhcp', 'boolean'],
                ['dhcp', 'default', 'value' => true],
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
                'start_date' => 'Konfiguracja',
                'dhcp' => 'DHCP',
                'smtp' => 'SMTP',
            ]
        ); 
	}
}