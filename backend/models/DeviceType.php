<?php

namespace backend\models;

use backend\models\Device;
use Yii;

/**
 * @property integer $id
 * @property string $name
 * @property boolean $list
 * @property boolean $children

 */
class DeviceType extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return '{{device_type}}';
	}

	public function getModelDevice(){
	
		return $this->hasMany(Device::className(), ['type'=>'id']);
	}
	
	public function rules()
	{
		return [
			[['name'], 'required'],
			[['list', 'children'], 'boolean'],	
			[['name'], 'safe'],
		];
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Nazwa',
		);
	}
	
	public static function findOrderName(){
		
		return self::find()->select(['id', 'name'])->orderBy('name');
	}
}
