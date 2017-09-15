<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use backend\models\Device;
/**
 * This is the model class for table "tbl_manufacturer".
 *
 * The followings are the available columns in table 'tbl_manufacturer':
 * @property integer $id
 * @property string $name
 */
class Manufacturer extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return '{{manufacturer}}';
	}

	public function getDevice(){
	
		//Wiele urzadzeń danego modelu
		return $this->hasMany(Device::className(), ['manufacturer'=>'id']);
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
