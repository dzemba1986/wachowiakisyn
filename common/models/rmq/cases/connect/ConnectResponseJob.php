<?php
namespace common\models\rmq\cases\services;

use common\models\address\Address;
use common\models\rmq\Job;
use yii\helpers\ArrayHelper;

class ConnectResponseJob extends Job {
    
    const TYPE = 2;

    /**
     * @var Address
     */
    
    public $services;
    
    public function __construct($caseId, $address) {
        
        parent::__construct();
        
        $this->case_id = $caseId;
        $this->services = \Yii::createObject([
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
        ])->serviceRange->rmqServices;
    }
    
    public function fields() {
        
        return ArrayHelper::merge(
            parent::fields(),
            [
                'services'
            ]
        );
    }
    
    public function afterSave($insert, $changeAttributes) {
        
        \Yii::$app->queueSeuSoa->push($this->toArray());
    }
}