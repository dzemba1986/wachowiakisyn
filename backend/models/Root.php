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

class Root extends Device
{
	const TYPE = 9;
	
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
	            ['model_id', 'required', 'message' => 'Wartość wymagana'],
	            
	            [['model_id'], 'safe'],
	        ]
	    );
	}
}
