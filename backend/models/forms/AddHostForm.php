<?php
namespace backend\models\forms;

use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\base\Model;
use backend\models\Host;

class AddHostForm extends Model {
    
    public $deviceId;
    public $port;
    public $vlanId;
    public $subnetId;
    public $ip;
    public $typeId;
    public $mac;
    public $address;
    
    public function rules() {
        
        return [
            ['port', 'integer'],
            ['port', 'required', 'message' => 'Wartość wymagana'],
            
            ['deviceId', 'integer'],
            ['deviceId', 'required', 'message' => 'Wartość wymagana'],
            
            ['subnetId', 'integer'],
            ['subnetId', 'required', 'message' => 'Wartość wymagana'],
            
            ['ip', 'string'],
            ['ip', 'required', 'message' => 'Wartość wymagana'],
            
            ['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort' => 'Za mało znaków', 'tooLong' => 'Za dużo znaków'],
            ['mac', 'required', 'message' => 'Wartość wymagana'],
            ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
            ['mac', 'unique', 'targetClass' => Host::className(), 'message' => 'Mac zajęty'],
            
            [['port', 'deviceId', 'vlanId', 'subnetId', 'ip'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        
        return [
            'deviceId' => 'Urządzenie',
            'port' => 'Port',
            'vlanId' => 'Vlan',
            'subnetId' => 'Podsieć',
            'ip' => 'Ip',
            'mac' => 'Mac',
        ];
    }
}