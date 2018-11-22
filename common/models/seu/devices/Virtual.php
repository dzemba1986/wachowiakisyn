<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\VirtualQuery;
use common\models\seu\devices\traits\Config;
use common\models\seu\devices\traits\Ip;
use common\models\seu\devices\traits\OneLink;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\helpers\ArrayHelper;

class Virtual extends Device {
    
    use Ip, OneLink, Config;
    
	const TYPE = 7;
	private $conf;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'mac',
	            'dhcp',
	            'smtp',
	        ]
	        );
	}
	
	public static function find() {
	    
		return new VirtualQuery(get_called_class(), ['type_id' => self::TYPE]);
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
	            ['mac', 'default', 'value' => null],
	            ['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
	            ['mac', MacaddressValidator::className(), 'message' => 'Zły format', 'skipOnEmpty' => true],
	            ['mac', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => true],
	            ['mac', 'unique', 'targetClass' => self::className(), 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
	                return strtolower($model->{$attribute}) !== strtolower($model->getOldAttribute($attribute));
	            }, 'filter' => ['status' => true], 'on' => [self::SCENARIO_CREATE, self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
	            ['mac', 'trim', 'skipOnEmpty' => true],
	            
	            ['dhcp', 'boolean'],
	            ['dhcp', 'default', 'value' => true],
	            ['dhcp', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['smtp', 'boolean'],
	            ['smtp', 'default', 'value' => false],
	            ['smtp', 'required', 'message' => 'Wartość wymagana'],
            ]
        );
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'dhcp', 'snmp']);
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'mac' => 'Mac',
	            'dhcp' => 'DHCP',
	            'smtp' => 'SMTP',
	        ]
        );
	}
}