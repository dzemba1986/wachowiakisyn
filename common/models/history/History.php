<?php

namespace common\models\history;

use backend\modules\address\models\Address;
use common\models\User;
use common\models\seu\devices\Device;
use common\models\soa\Connection;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $desc
 * @property integer $type_id
 * @property integer $device_id
 * @property integer $address_id
 * @property integer $connection_id
 * @property integer $created_by
 */

class History extends \yii\db\ActiveRecord
{
	
	public static function tableName() {
	    
		return '{{history}}';
	}

	public function rules()
	{
		return [
			['created_at', 'required'],
		    ['created_at', 'date', 'format'=>'php:Y-m-d H:i:s'],
		    
		    ['created_by', 'integer'],
		    ['created_by', 'required'],
		    
		    ['desc', 'string'],
				
			['address_id', 'integer'],
			['address_id', 'required'],
		    
		    ['connection_id', 'integer'],
		    
		    ['device_id', 'integer'],
				
			[['created_at', 'created_by', 'desc', 'type_id', 'device_id', 'address_id', 'connection_id'], 'safe'],
		];
	}
	
	public function attributeLabels()
	{
		return array(
			'creatrd_at' => 'Kiedy',
			'desc' => 'Opis',
			'type_id' => 'Typ',
			'address_id' => 'Adres',
		    'device_id' => 'Urządzenie',
		    'connection_id' => 'Umowa',
		    'created_by' => 'Utworzył'
		);
	}
    
    public function getAddress(){
	
		return $this->hasOne(Address::className(), ['id' => 'address_id']);
	}
	
	public function getDevice(){
	    
	    return $this->hasOne(Device::className(), ['id' => 'device_id']);
	}
	
	public function getConnection(){
	    
	    return $this->hasOne(Connection::className(), ['id' => 'connection_id']);
	}
	
	public function getUser(){
	    
	    return $this->hasOne(User::className(), ['id' => 'created_by']);
	}
}