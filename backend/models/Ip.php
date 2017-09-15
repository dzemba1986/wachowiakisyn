<?php

namespace backend\models;

use Yii;
/**
 * This is the model class for table "ip".
 *
 * The followings are the available columns in table 'ip':
 * @property string $ip
 * @property integer $main
 * @property integer $subnet
 * @property integer $device
 */
class Ip extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return '{{ip}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['ip', 'required', 'message' => 'Wartość wymagana'],
			
			['subnet', 'required', 'message' => 'Wartość wymagana'],
			['subnet', 'integer', 'message' => 'Wartość musi być liczbą'],
				
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			[['ip', 'main', 'subnet', 'device'], 'safe'],
		];
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ip' => 'Adres IP',	
			'main' => 'Główny',
			'subnet' => 'Podsieć',
			'device' => 'Urządzenie',
		);
	}
	
	public function getModelSubnet(){
	
		//Connection ma tylko 1 Address
		return $this->hasOne(Subnet::className(), ['id' => 'subnet']);
	}
	
	public function getModelDevice(){
	
		//Connection ma tylko 1 Address
		return $this->hasOne(Device::className(), ['id' => 'device']);
	}
}
