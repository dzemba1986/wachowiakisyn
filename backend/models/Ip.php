<?php

namespace backend\models;

use yii\db\ActiveRecord;

/**
 * @property string $ip
 * @property boolean $main
 * @property integer $subnet_id 
 * @property integer $device_id
 * @property backend\models\Subnet $subnet
 * @property backend\models\Device $device
 */

class Ip extends ActiveRecord
{
    const EVENT_NEW_IP = 'new-ip';
    const EVENT_DELETE_IP = 'delete-ip';
    
	public static function tableName() : string {
		
		return '{{ip}}';
	}
	
	public function init() {
	    
	    parent::init();
	    $this->on(self::EVENT_NEW_IP, ['backend\models\HistoryIp', 'createIp']);
	    $this->on(self::EVENT_DELETE_IP, ['backend\models\HistoryIp', 'deleteIp']);
	}

	public function rules() : array {
		
		return [
		    ['ip', 'string'],
			['ip', 'required', 'message' => 'Wartość wymagana'],
			
		    ['subnet_id', 'required', 'message' => 'Wartość wymagana'],
		    ['subnet_id', 'integer', 'message' => 'Wartość musi być liczbą'],
		    
			['device_id', 'required', 'message' => 'Wartość wymagana'],
			['device_id', 'integer', 'message' => 'Wartość musi być liczbą'],
				
			[['ip', 'main', 'subnet', 'device'], 'safe'],
		];
	}
	
	public function attributeLabels() : array {
		
		return [
			'ip' => 'Adres IP',	
			'main' => 'Główny',
			'subnet_id' => 'Podsieć',
			'device_id' => 'Urządzenie',
		];
	}
	
	public function getSubnet() {
	
		return $this->hasOne(Subnet::className(), ['id' => 'subnet_id']);
	}
	
	public function getDevice() {
	
		return $this->hasOne(Device::className(), ['id' => 'device_id']);
	}
}
