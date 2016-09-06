<?php

namespace backend\models;

use backend\models\Address;
use backend\models\Model;
/**
 * This is the model class for table "device".
 *
 * The followings are the available columns in table 'device':
 * @property integer $id
 * @property integer $status
 * @property string $name
 * @property string $desc
 * @property integer $address
 * @property integer $type
 */

class Root extends Device
{
	const TYPE = 9; //typ dal ROOT
	
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
}
