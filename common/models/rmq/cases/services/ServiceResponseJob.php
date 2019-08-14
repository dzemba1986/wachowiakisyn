<?php
namespace common\models\rmq\cases\services;

use common\models\address\Address;
use common\models\rmq\Job;
use yii\helpers\ArrayHelper;

class ServiceResponseJob extends Job {
    
    const TYPE = 2;

    public $services;
    
    public function __construct($caseId, $address, $config = []) {
        
        $this->case_id = $caseId;
        $range = \Yii::createObject([
            'class' => Address::class,
            't_woj' => $address['teryt_woj'],
            't_pow' => $address['teryt_pow'],
            't_gmi' => $address['teryt_gmi'],
            't_rodz' => $address['teryt_rodz'],
            't_miasto' => $address['teryt_miasto'],
            't_ulica' => $address['teryt_ulica'],
            'ulica_prefix' => $address['ulica_prefix'],
            'ulica' => $address['ulica'],
            'dom' => $address['dom'],
            'dom_szczegol' => $address['dom_szczegol'],
            'lokal' => $address['lokal'],
            'lokal_szczegol' => $address['lokal_szczegol'],
        ])->serviceRange;
        
        if ($range) $this->services = $range->rmqServices;
        else $this->services = [];
        
        parent::__construct($config);
    }
    
    public function rules() {
        
        return [
            ['services', 'each', 'rule' => ['required'], 'message' => 'Wartość wymagana'],
            ['services', 'each', 'rule' => ['trim']],
        ];
    }
    
    public function fields() {
        
        return ArrayHelper::merge(
            parent::fields(),
            [
                'services'
            ]
        );
    }
}