<?php

namespace frontend\modules\seu\models\forms;

use yii\base\Model;

class AddHostRfogForm extends Model {
    
    public $deviceId;
    public $port;
    public $typeId;
    public $address;
    
    public function rules() {
        
        return [
            ['port', 'integer'],
            ['port', 'required', 'message' => 'Wartość wymagana'],
            
            ['deviceId', 'integer'],
            ['deviceId', 'required', 'message' => 'Wartość wymagana'],
            
            [['port', 'deviceId'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        
        return [
            'deviceId' => 'Urządzenie',
            'port' => 'Port',
        ];
    }
}