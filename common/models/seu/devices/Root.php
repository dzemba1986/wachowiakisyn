<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\DeviceQuery;
use common\models\seu\devices\traits\OneLink;
use common\models\seu\devices\traits\ParentDevice;

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

class Root extends Device {
    
	use OneLink, ParentDevice;
    
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
		if(!$insert) $this->type_id = self::TYPE;
		
		return parent::beforeSave($insert);
	}
}