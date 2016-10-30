<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use backend\models\Device;
/**
 * This is the model class for table "tbl_package".
 *
 * The followings are the available columns in table 'tbl_package':
 * @property integer $id
 * @property string $name
 * @property boolean $list
 * @property boolean $children

 */
class DeviceType extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'device_type';
	}

	public function getModelDevice(){
	
		//Wiele instalacji na danym adresie
		return $this->hasMany(Device::className(), ['type'=>'id']);
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
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
}
