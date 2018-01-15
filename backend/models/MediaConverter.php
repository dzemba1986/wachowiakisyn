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
 * @property string $serial
 * @property integer $model_id
 * @property integer $manufacturer_id
 */

class MediaConverter extends Device
{
	const TYPE = 8;
	
	public function init()
	{
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public static function find()
	{
		return new DeviceQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert)
	{
		if(!$insert) 
			$this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}
	
	public function rules(){
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['mac', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['serial', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['model_id', 'required', 'message' => 'Wartość wymagana'],
	            
	            [['mac', 'serial', 'manufacturer_id', 'model_id'], 'safe'],
	        ]
		);
	}
	
	public function scenarios(){
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE],['mac', 'serial', 'manufacturer_id', 'model_id']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'serial']);
	    
	    return $scenarios;
	}
}