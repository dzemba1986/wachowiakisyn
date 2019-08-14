<?php
namespace common\models\rmq\cases\services;

use common\models\rmq\Job;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;
use yii\validators\RequiredValidator;

class ServiceRequestJob extends Job implements JobInterface {
    
    const TYPE = 1;
    
    public $address;
    public $teryt_woj;
    public $teryt_pow;
    public $teryt_gmi;
    public $teryt_rodz;
    public $teryt_miasto;
    public $teryt_ulica;
    public $ulica_prefix;
    public $ulica;
    public $dom;
    public $dom_szczegol;
    public $lokal;
    public $lokal_szczegol;
    
    public function init() {
        
        parent::init();
        
        foreach ($this->address as $key => $value) {
            if (property_exists($this, $key)) $this->$key = $value;
        }
    }
    
    public function rules() {
        
        return [
            ['teryt_woj', 'required', 'message' => 'Wartość wymagana'],
            ['teryt_woj', 'string', 'min' => 2, 'max' => 2],
            ['teryt_woj', 'trim'],

            ['teryt_pow', 'required', 'message' => 'Wartość wymagana'],
            ['teryt_pow', 'string', 'min' => 2, 'max' => 2],
            ['teryt_pow', 'trim'],
            
            ['teryt_gmi', 'required', 'message' => 'Wartość wymagana'],
            ['teryt_gmi', 'string', 'min' => 2, 'max' => 2],
            ['teryt_gmi', 'trim'],
            
            ['teryt_rodz', 'required', 'message' => 'Wartość wymagana'],
            ['teryt_rodz', 'string', 'min' => 1, 'max' => 1],
            ['teryt_rodz', 'trim'],
            
            ['teryt_miasto', 'required', 'message' => 'Wartość wymagana'],
            ['teryt_miasto', 'string', 'min' => 7, 'max' => 7],
            ['teryt_miasto', 'trim'],
            
            ['teryt_ulica', 'required', 'message' => 'Wartość wymagana'],
            ['teryt_ulica', 'string', 'min' => 5, 'max' => 7],
            ['teryt_ulica', 'trim'],
            
            ['ulica_prefix', 'required', 'message' => 'Wartość wymagana'],
            ['ulica_prefix', 'string', 'min' => 1, 'max' => 7],
            ['ulica_prefix', 'trim'],
            
            ['ulica', 'required', 'message' => 'Wartość wymagana'],
            ['ulica', 'string', 'min' => 2, 'max' => 255],
            ['ulica', 'trim'],

            ['dom', 'required', 'message' => 'Wartość wymagana'],
            ['dom', 'string', 'min' => 1, 'max' => 10],
            ['dom', 'trim'],

            ['dom_szczegol', 'string', 'min' => 1, 'max' => 50],
            ['dom_szczegol', 'filter', 'filter' => 'strtoupper'],
            ['dom_szczegol', 'default', 'value' => ''],
            ['dom_szczegol', 'trim'],
            
            ['lokal', 'string', 'min' => 1, 'max' => 10],
            ['lokal', 'default', 'value' => ''],
            ['lokal', 'trim'],
            
            ['lokal_szczegol', 'string', 'min' => 1, 'max' => 50],
            ['lokal_szczegol', 'default', 'value' => ''],
            ['lokal_szczegol', 'filter', 'filter'=>'strtoupper'],
            ['lokal_szczegol', 'trim'],
        ];
    }
    
    public function fields() {
        
        return ArrayHelper::merge(
            parent::fields(),
            [
                'address'
            ]
        );
    }
    
    public function afterValidate() {
        
        foreach ($this->address as $prop => $value) {
            if (property_exists($this, $prop))
                if ($value != $this->$prop) $this->address[$prop] = $this->$prop;
        }
        
        return parent::afterValidate();
    }
    
    public function execute($queue) {
        
        $response = \Yii::createObject([
            'class' => ServiceResponseJob::class,
        ], [$this->case_id, $this->address]);
        
        \Yii::$app->queueSeuSoa->push($response);
    }
}

