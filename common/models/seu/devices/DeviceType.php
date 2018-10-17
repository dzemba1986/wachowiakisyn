<?php

namespace common\models\seu\devices;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property boolean $list
 * @property boolean $children
 * @property string $controller
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
	
	public static function findByController(){
	    
	    return self::find()->select(['controller', 'name'])->where(['list' => true])->orderBy('name');
	}
}