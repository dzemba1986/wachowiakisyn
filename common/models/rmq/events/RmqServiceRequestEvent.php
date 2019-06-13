<?php
namespace common\models\rmq\events;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class RmqServiceRequestEvent extends RmqEvent {
    
    const TYPE = 1;
    
    public $t_woj;
    public $t_pow;
    public $t_gmi;
    public $t_rodz;
    public $t_miasto;
    public $t_ulica;
    public $ulica_prefix;
    public $ulica;
    public $dom;
    public $dom_szczegol;
    public $lokal;
    public $lokal_szczegol;
    
    public function rules() {
        
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['t_woj', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_pow', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_gmi', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_rodz', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_miasto', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_miasto', 'required', 'message' => 'Wartość wymagana'],

                ['t_ulica', 'required', 'message' => 'Wartość wymagana'],
                
                ['ulica_prefix', 'required', 'message' => 'Wartość wymagana'],

                ['ulica', 'required', 'message' => 'Wartość wymagana'],
                
                ['dom', 'string', 'min' => 1, 'max' => 10],
                ['dom', 'required', 'message' => 'Wartość wymagana'],
                ['dom', 'trim'],
                
                ['dom_szczegol', 'string', 'min' => 1, 'max' => 50],
                ['dom_szczegol', 'default', 'value' => ''],
                ['dom_szczegol', 'filter', 'filter' => 'strtoupper'],
                ['dom_szczegol', 'trim'],
                
                ['lokal', 'string', 'min' => 1, 'max' => 10],
                ['lokal', 'default', 'value' => ''],
                ['lokal', 'trim'],
                
                ['lokal_szczegol', 'string', 'min' => 1, 'max' => 50],
                ['lokal_szczegol', 'default', 'value' => ''],
                ['lokal_szczegol', 'filter', 'filter'=>'strtoupper'],
                ['lokal_szczegol', 'trim'],
            ]
        );
    }
    
    public function toObject($json) {
        
        parent::toObject($json);
        
        $this->t_woj = $json['address']['teryt_woj'];
        $this->t_pow = $json['address']['teryt_pow'];
        $this->t_gmi = $json['address']['teryt_gmi'];
        $this->t_rodz = $json['address']['teryt_rodz'];
        $this->t_miasto = $json['address']['teryt_miasto'];
        $this->t_ulica = $json['address']['teryt_ulica'];
        $this->ulica_prefix = $json['address']['ulica_prefix'];
        $this->ulica = $json['address']['ulica'];
        $this->dom = $json['address']['dom'];
        $this->dom_szczegol = $json['address']['dom_szczegol'];
        $this->lokal = $json['address']['lokal'];
        $this->lokal_szczegol = $json['address']['lokal_szczegol'];
        
        if ($this->validate()) return $this;
        else return false;
    }
    
    public function beforeValidate() {
        
        $json = [
            'address' => [
                'teryt_woj' => $this->t_woj,            
                'teryt_pow' => $this->t_pow,            
                'teryt_gmi' => $this->t_gmi,            
                'teryt_rodz' => $this->t_rodz,            
                'teryt_miasto' => $this->t_miasto,            
                'teryt_ulica' => $this->t_ulica,            
                'ulica_prefix' => $this->ulica_prefix,            
                'ulica' => $this->ulica,            
                'dom' => $this->dom,            
                'dom_szczegol' => $this->dom_szczegol,            
                'lokal' => $this->lokal,            
                'lokal_szczegol' => $this->lokal_szczegol,            
            ]
        ];
        
        $this->message = Json::encode($json);
        
        return parent::beforeValidate();
    }
    
    public function afterSave($insert, $changeAttributes) {
        
        if ($insert) {
            $response = new RmqServiceResponseEvent($this);
//             var_dump($response->toArray()); exit();
//             $response->validate();
            if (!$response->save()) var_dump($response->errors);
        }
    }
    
//     private function getAddress() {
        
//         $address = \Yii::createObject(Address::class, [
//             't_ulica' => $this->t_ulica,
//             'dom' => $this->dom,
//             'dom_szczegol' => $this->dom_szczegol,
//             'lokal' => $this->lokal,
//             'lokal_szczegol' => $this->lokal_szczegol,
//         ]);
        
//         $address->beforeSave(true);
        
//         return $address;
//     }
    
}

