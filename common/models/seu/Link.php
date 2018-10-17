<?php

namespace common\models\seu;

use common\models\seu\devices\Device;

/**
 * @property intiger $device
 * @property integer $port
 * @property integer $parent_device
 * @property integer $parent_port
 */

class Link extends \yii\db\ActiveRecord
{
    public static function tableName() {
        
        return '{{agregation}}';
    }

    public function rules() {
        
        return [	
        		
        	['device', 'required', 'message'=>'Wartość wymagana'],
        		
        	['port', 'required', 'message'=>'Wartość wymagana'],
        		
        	['parent_device', 'required', 'message'=>'Wartość wymagana'],
        		
        	['parent_port', 'required', 'message'=>'Wartość wymagana'],
            
            [['device', 'port', 'parent_device', 'parent_port'], 'safe'],
        ];
    }
    
    public function getDevice() {
    
    	return $this->hasOne(Device::className(), ['id' => 'device']);
    }
    
    public function getParent() {
        
        return $this->hasOne(Device::className(), ['id' => 'parent_device']);
    }
    
    public function attributeLabels() {
        
        return [
            'device' => 'Urządzenie',
            'port' => 'Port',
            'parent_device' => 'Urzadzenie nadrzędne',
            'parent_port' => 'Port nadrzędny',
        ];
    }
}


