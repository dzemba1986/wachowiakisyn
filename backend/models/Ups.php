<?php

namespace backend\models;

use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\helpers\ArrayHelper;

class Ups extends Device
{
	const TYPE = 10;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'dhcp',
	            'monitoring',
	        ]
        );
	}
	
	public static function find() {
	    
		return new DeviceQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
	    if (!$insert) {
	        if (array_key_exists('monitoring', $this->dirtyAttributes) && !$this->oldAttributes['monitoring'] && $this->monitoring) {
	            
	            \Yii::$app->apiIcingaClient->put('objects/hosts/' . $this->id, [
	                "templates" => [ $this->model->name ],
	                "attrs" => [
	                    'display_name' => $this->mixName,
	                    'address' => $this->mainIp->ip,
	                    'vars.device' => 'UPS',
	                    'vars.model' => $this->model->name,
	                ]
	            ], [
	                'Content-Type' => 'application/json',
	                'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
	                'Accept' => 'application/json'
	            ])->send();
	            
	            \Yii::$app->apiIcingaClient->post('actions/restart-process', null, [
	                'Content-Type' => 'application/json',
	                'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
	                'Accept' => 'application/json'
	            ])->send();
	        }
	        
	        if (array_key_exists('monitoring', $this->dirtyAttributes) && $this->oldAttributes['monitoring'] && !$this->monitoring) {
	            
	            \Yii::$app->apiIcingaClient->delete("objects/hosts/{$this->id}?cascade=1", null, [
	                'Content-Type' => 'application/json',
	                'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
	                'Accept' => 'application/json'
	            ])->send();
	        }
	        
	        if ((array_key_exists('name', $this->dirtyAttributes) || array_key_exists('proper_name', $this->dirtyAttributes)) && $this->monitoring) {
	            
	            \Yii::$app->apiIcingaClient->delete("objects/hosts/{$this->id}?cascade=1", null, [
	                'Content-Type' => 'application/json',
	                'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
	                'Accept' => 'application/json'
	            ])->send();
	            
	            \Yii::$app->apiIcingaClient->put('objects/hosts/' . $this->id, [
	                "templates" => [ $this->model->name ],
	                "attrs" => [
	                    'display_name' => $this->mixName,
	                    'address' => $this->mainIp->ip,
	                    'vars.device' => 'Camera',
	                    'vars.model' => $this->model->name,
	                ]
	            ], [
	                'Content-Type' => 'application/json',
	                'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
	                'Accept' => 'application/json'
	            ])->send();
	            
	            \Yii::$app->apiIcingaClient->post('actions/restart-process', null, [
	                'Content-Type' => 'application/json',
	                'Authorization' => 'Basic YXBpOmFwaXBhc3M=',
	                'Accept' => 'application/json'
	            ])->send();
	        }
	    }
	    
		if (!$insert)  $this->type_id = self::TYPE;
		
		return parent::beforeSave($insert);
	}
	
	public function rules() {
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['mac', 'required', 'message' => 'Wartość wymagana'],
	            ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
	            
	            ['serial', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['model_id', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['dhcp', 'boolean'],
	            ['dhcp', 'default', 'value' => false],
	            ['dhcp', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['monitoring', 'boolean'],
	            
	            [['serial', 'manufacturer_id', 'model_id', 'dhcp', 'monitoring'], 'safe'],
	        ]
		);
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE],['serial', 'manufacturer_id', 'model_id', 'mac', 'dhcp', 'monitoring']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['serial', 'mac', 'dhcp', 'monitoring']);
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'monitoring' => 'Monitorować',
	        ]
	        );
	}
}