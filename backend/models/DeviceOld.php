<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
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
        return 'device';
    }
    
    public function getModelDeviceVoip(){
	
		//urządzenie ma jeden adres
		return $this->hasOne(DeviceVoipOld::className(), ['device'=>'dev_id']);
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
