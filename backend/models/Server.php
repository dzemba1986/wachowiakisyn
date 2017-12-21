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

class Server extends Device
{
	const TYPE = 4;
	
	public function init()
	{
		$this->type = self::TYPE;
		parent::init();
	}
	
	public static function find()
	{
		return new DeviceQuery(get_called_class(), ['type' => self::TYPE]);
	}
	
	public function beforeSave($insert)
	{
		if(!$insert) 
			$this->type = self::TYPE;
		return parent::beforeSave($insert);
	}
	
	public function rules(){
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['mac', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['serial', 'required', 'message' => 'Wartość wymagana'],
	        ]
	        );
	}
	
	public function getIps(){
	    
	    return $this->hasMany(Ip::className(), ['device' => 'id'])->orderBy(['main' => SORT_DESC]);
	}
}
