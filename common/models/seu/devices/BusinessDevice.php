<?php

namespace common\models\seu\devices;

use common\models\seu\Link;
use common\models\seu\Manufacturer;
use common\models\seu\Model;
use common\models\seu\network\Ip;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * @property string $mac
 * @property string $serial
 * @property integer $model_id
 * @property integer $manufacturer_id
 * @property backend\models\Manufacturer $manufacturer
 * @property backend\models\Model $model
 */

abstract class BusinessDevice extends Device {
    
	private $modelName = NULL;
	private $manufacturerName = NULL;
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'serial',
	            'mac',
	            'manufacturer_id',
	            'model_id',
	        ]
        );
	}
	
	public function fields() {
	    
	    return ArrayHelper::merge(
	        parent::fields(),
	        [
	            'serial',
	            'mac',
	            'manufacturer_id',
	            'model_id',
	        ]
        );
	}
	
	public function rules() {
		
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            'mac_required' => ['mac', 'required', 'message' => 'Wartość wymagana'],
	            ['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
	            ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
	            ['mac', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => TRUE],
	            'mac_unique' => ['mac', 'unique', 'targetClass' => static::className(), 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
	                return strtolower($model->{$attribute}) !== strtolower($model->getOldAttribute($attribute));
	            }],
	            ['mac', 'trim', 'skipOnEmpty' => TRUE],
	            
	            ['serial', 'required', 'message' => 'Wartość wymagana'],
	            ['serial', 'string', 'max' => 30, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
	            ['serial', 'unique', 'targetClass' => BusinessDevice::className(), 'message' => 'Serial zajęty', 'when' => function ($model, $attribute) {
	                return $model->{$attribute} !== $model->getOldAttribute($attribute);
	            }],
	            ['serial', 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => TRUE],
	            ['serial', 'trim'],
	            
	            ['manufacturer_id', 'integer'],
	            ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['model_id', 'integer'],
	            ['model_id', 'required', 'message' => 'Wartość wymagana'],
            ]
        );      
	}
    
	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['mac', 'serial', 'model_id', 'manufacturer_id'];
			
		return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'mac' => 'Mac',
	            'serial' => 'Serial',
	            'manufacturer_id' => 'Producent',
	            'model_id' => 'Model',
	        ]
        );
	}
	
	public function getModel() {
	    
	    return $this->hasOne(Model::className(), ['id' => 'model_id']);
	}
	
	public function getManufacturer() {
	    
	    return $this->hasOne(Manufacturer::className(), ['id' => 'manufacturer_id']);
	}
	
    public function getModelName() : string {
        
        if (is_null($this->modelName)) $this->modelName = $this->getModel()->select('name')->asArray()->one()['name'];
        
        return $this->modelName;
    }
    
    public function getManufacturerName() : string {
        
        if (is_null($this->manufacturerName)) $this->manufacturerName = $this->getManufacturer()->select('name')->asArray()->one()['name'];
        
        return $this->manufacturerName;
    }
    
    public function replace($post) {
        
        $dId = (int) $post['dId'];
        $map = isset($post['map']) ? $post['map'] : NULL;
        
        if (is_null($map)) {
            $countUpdate = 0;
            $countUpdate += Link::updateAll(['device' => $dId], ['device' => $this->id]);
            $countUpdate += Link::updateAll(['parent_device' => $dId], ['parent_device' => $this->id]);
            if ($countUpdate == 0) throw new Exception('Nie znalazł linku');
        } elseif (is_array($map) && !empty($map)) {
            foreach ($map as $oldPort => $newPort) {
                $countUpdate = 0;
                $countUpdate = Link::updateAll(['parent_device' => $dId, 'parent_port' => $newPort], ['parent_device' => $this->id, 'parent_port' => $oldPort]);
                if ($countUpdate == 0) $countUpdate = Link::updateAll(['device' => $dId, 'port' => $newPort], ['device' => $this->id, 'port' => $oldPort]);
                if ($countUpdate == 0) throw new Exception('Nie znalazł linku');
            }
        }
        
        Ip::updateAll(['device_id' => $dId], ['device_id' => $this->id]);
        
        $this->scenario = $this::SCENARIO_REPLACE;
        $destination = Device::findOne($dId);
        $destination->scenario = $destination::SCENARIO_REPLACE;
        
        $destination->address_id = $this->address_id;
        $destination->status = $this->status;
        $destination->name = $this->name;
        $destination->proper_name = $this->proper_name;
        
        $this->address_id = 1;
        $this->status = NULL;
        $this->name = NULL;
        $this->proper_name = NULL;
        
        if (method_exists($this, 'replaceParams')) $this->replaceParams($destination, $post);
        if (!($this->save() && $destination->save())) throw new Exception('Błąd zapisu urządzenia');
    }
}