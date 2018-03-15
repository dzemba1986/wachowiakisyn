<?php

namespace backend\models;

use backend\models\configuration\ECSeriesConfiguration;
use backend\models\configuration\GSSeriesConfiguration;
use backend\models\configuration\XSeriesConfiguration;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
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
 * @property string $serial
 * @property integer $model_id
 * @property integer $manufacturer_id
 * @property integer $alias
 */

class Camera extends Device
{
	const TYPE = 6;
	private $conf;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'alias',
	            'dhcp'
	        ]
	        );
	}
	
	public static function find() {
	    
		return new DeviceQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert)
	{
		if(!$insert) 
			$this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}
	
	public function rules() {
		
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['mac', 'required', 'message' => 'Wartość wymagana'],
                ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
                
                ['serial', 'required', 'message' => 'Wartość wymagana'],
                
                ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
                
                ['model_id', 'required', 'message' => 'Wartość wymagana'],
                
            	['alias', 'string', 'min' => 2, 'max' => 30],
                ['alias', 'required', 'message' => 'Wartość wymagana', 'when' => function ($model){ isset($model->status); }],
                
                ['dhcp', 'boolean'],
                ['dhcp', 'default', 'value' => false],
                ['dhcp', 'required', 'message' => 'Wartość wymagana'],
            		
                [['mac', 'serial', 'manufacturer_id', 'model_id', 'alias'], 'safe'],
            ]
        );       
	}

	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE],['mac', 'serial', 'manufacturer_id', 'model_id']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'serial', 'alias', 'dhcp']);
		$scenarios[self::SCENARIO_REPLACE] = ArrayHelper::merge($scenarios[self::SCENARIO_REPLACE], ['alias', 'dhcp']);
			
		return $scenarios;
	}
    
	public function attributeLabels() {
	    
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
            	'alias' => 'Nazwa w monitoringu',
            ]
        ); 
	}
	
	function afterSave($insert, $changedAttributes) {
	    
	    if (!$insert) {
	        if (isset($changedAttributes['mac']) || isset($changedAttributes['dhcp'])) {
	            !empty($this->ips) ? Dhcp::generateFile($this->ips[0]->subnet) : null;
	        }
	    }
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
