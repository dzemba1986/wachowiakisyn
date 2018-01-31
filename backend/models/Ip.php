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
	public static function tableName() : string {
		
		return '{{ip}}';
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
	
	public function afterSave($insert, $changedAttributes) {
	    
	    //FIXME należy dodać ograniczenie na hosty/podsieci które chcemy rejestrować w historii
	    if ($insert) {
    	    $device = Device::findOne($this->device_id);
    	    
    	    $historyIp = new HistoryIp();
    	    $historyIp->scenario = HistoryIp::SCENARIO_CREATE_IP;
    	    $historyIp->ip = $this->ip;
    	    $historyIp->from_date = date('Y-m-d H:i:s');
    	    $historyIp->address_id = $device->address_id;
    	    
    	    try {
    	        if (!$historyIp->save()) throw new \Exception('Problem z zapisem historii IP - dodanie IP');
    	        
    	        return true;
    	    } catch (\Throwable $t) {
    	        var_dump($historyIp->errors);
    	        var_dump($t->getMessage());
    	        exit();
    	    }
	    }
	}
	
	public function beforeDelete() {
	    
	    if (!parent::beforeDelete()) {
	        return false;
	    }
	    
	    //FIXME należy dodać ograniczenie na hosty/podsieci które chcemy rejestrować w historii
	    $device = Device::findOne($this->device_id);
	    
	    $historyIp = HistoryIp::findOne(['ip' => $this->ip, 'address_id' => $device->address_id, 'to_date' => null]);
	    $historyIp->scenario = HistoryIp::SCENARIO_DELETE_IP;
	    $historyIp->to_date = date('Y-m-d H:i:s');
	    
	    try {
	        if (!$historyIp->save()) throw new \Exception('Problem z zapisem historii IP - usunięcie IP');
	        
	        return true;
	    } catch (\Throwable $e) {
	        var_dump($historyIp->errors);
	        var_dump($t->getMessage());
	        exit();
	    }
	}
}
