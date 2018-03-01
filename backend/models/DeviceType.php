<?php

namespace backend\models;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property boolean $list
 * @property boolean $children
 */

class DeviceType extends ActiveRecord
{
	public static function tableName()
	{
		return '{{device_type}}';
	}

	public function rules()
	{
		return [
			[['name'], 'required'],
			[['list', 'children'], 'boolean'],	
			[['name'], 'safe'],
		];
	}
	
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Nazwa',
		);
	}
	
	public static function findOrderName(){
		
	    return self::find()->select(['id', 'name'])->where(['list' => true])->orderBy('name');
	}
}
