<?php

namespace backend\models;

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
 * @property boolean distribution
 */

class Swith extends Device
{
	const TYPE = 2;
	private $conf;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'distribution',
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
                ['mac', 'required', 'message' => 'Wartość wymagana'],
                ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
                
                ['serial', 'required', 'message' => 'Wartość wymagana'],
                
                ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
                
                ['model_id', 'required', 'message' => 'Wartość wymagana'],
                
                ['distribution', 'boolean'],
                ['distribution', 'required', 'message' => 'Wartość wymagana', 'when' => function ($model){ isset($model->status); }],
                
                [['mac', 'serial', 'manufacturer_id', 'model_id', 'distribution'], 'safe'],
            ]
        );       
	}

	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE],['mac', 'serial', 'manufacturer_id', 'model_id']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'serial', 'distribution']);
	    
	    return $scenarios;
	}

	public function attributeLabels() {
	    
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'distribution' => 'Szkieletowy',
            ]
        ); 
	}
	
	public function configurationAdd() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
	    if ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
	    
	    return $this->conf->add();
	}
	
	public function configurationDrop() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
	    if ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
	    
	    return $this->conf->drop();
	}
}