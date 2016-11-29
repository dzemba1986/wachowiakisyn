<?php

namespace backend\models;

use Yii;
use backend\models\Device;

class DeviceOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Device';
    }
    
    public function getModelAddressDeviceOld(){
    
    	//urządzenie ma jeden adres
    	return $this->hasOne(AddressDeviceOld::className(), ['id'=>'lokalizacja']);
    }
    
    public function getModelDeviceVoip(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(DeviceVoipOld::className(), ['device'=>'dev_id']);
	}
	
	public function getModelDeviceHost(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(DeviceHostOld::className(), ['device'=>'dev_id']);
	}
	
	public function getModelDeviceServer(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(DeviceServerOld::className(), ['device'=>'dev_id']);
	}
    
    public function getModelDeviceCamera(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(DeviceCameraOld::className(), ['device'=>'dev_id']);
	}
    
    public function getModelDeviceRouter(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(DeviceRouterOld::className(), ['device'=>'dev_id']);
	}
    
    public function getModelDeviceSwitchRejon(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(DeviceSwitchRejonOld::className(), ['device'=>'dev_id']);
	}
    
    public function getModelDeviceSwitchBud(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(DeviceSwitchBudOld::className(), ['device'=>'dev_id']);
	}
}
