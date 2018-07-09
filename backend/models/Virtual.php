<?php

namespace backend\models;

use backend\models\configuration\ECSeriesConfiguration;
use backend\models\configuration\GSSeriesConfiguration;
use backend\models\configuration\XSeriesConfiguration;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\helpers\ArrayHelper;

/**
 * @property boolean $dhcp
 */

class Virtual extends Device {
    
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
	            'dhcp',
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
	            ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
	            ['mac', 'filter', 'filter' => function ($value) {
	                return $value==='' ? null : $value;
	            }],
	            //hosty i virtualki mogą być w tej samej podsieci
	            ['mac', 'unique', 'targetClass' => Host::className(), 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
	                return strtolower($model->mac) !== strtolower($model->getOldAttribute('mac'));
	            }],
	            
	            ['dhcp', 'boolean'],
	            ['dhcp', 'default', 'value' => false],
	            ['dhcp', 'required', 'message' => 'Wartość wymagana'],
	            
	            [['mac', 'dhcp'], 'safe'],
            ]
        );
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac', 'dhcp']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'dhcp']);
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'dhcp' => 'DHCP',
	        ]
        );
	}
	
	public function configurationAdd() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
	        if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->add();
	}
	
	public function configurationDrop() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
	        if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->drop();
	}
	
	public function configurationChangeMac($newMac) {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
	        if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->changeMac($newMac);
	}
}