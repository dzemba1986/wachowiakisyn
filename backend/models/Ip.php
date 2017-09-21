<?php

namespace backend\models;

use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "ip".
 *
 * The followings are the available columns in table 'ip':
 * @property string $ip
 * @property boolean $main
 * @property integer $subnet 
 * @property integer $device
 * @property ActiveQueryInterface $modelSubnet
 * @property ActiveQueryInterface $modelDevice
 * 
 * @author Mikołajewski Daniel <daniel.mikolajewski@wachowiakisyn.pl>
 */
class Ip extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName() : string {
		
		return '{{ip}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() : array {
		
		return [
			['ip', 'required', 'message' => 'Wartość wymagana'],
			
			['subnet', 'required', 'message' => 'Wartość wymagana'],
			['subnet', 'integer', 'message' => 'Wartość musi być liczbą'],
				
			// The following rule is used by search().
			[['ip', 'main', 'subnet', 'device'], 'safe'],
		];
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() : array {
		
		return [
			'ip' => 'Adres IP',	
			'main' => 'Główny',
			'subnet' => 'Podsieć',
			'device' => 'Urządzenie',
		];
	}
	
	/**
	 * @return ActiceQueryInterface the relational query object
	 */
	public function getModelSubnet() : ActiveQueryInterface {
	
		//adres ip należy do 1 podsieci
		return $this->hasOne(Subnet::className(), ['id' => 'subnet']);
	}
	
	/**
	 * @return ActiceQueryInterface the relational query object
	 */
	public function getModelDevice() : ActiveQueryInterface {
	
		//adres ip należy do 1 urządzenia
		return $this->hasOne(Device::className(), ['id' => 'device']);
	}
}
