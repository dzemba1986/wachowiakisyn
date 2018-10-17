<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\RadioQuery;
use common\models\seu\devices\traits\Ip;
use common\models\seu\devices\traits\OneLink;
use common\models\seu\devices\traits\ParentDevice;
use yii\helpers\ArrayHelper;

class Radio extends BusinessDevice {
    
    use Ip, OneLink, ParentDevice;
    
	const TYPE = 11;
	
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
	            'geolocation'
	        ]
        );
	}
	
	public static function find() {
	    
		return new RadioQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
	    if (!$insert) {
	        if (array_key_exists('monitoring', $this->dirtyAttributes) && !$this->oldAttributes['monitoring'] && $this->monitoring) {
	            
	            \Yii::$app->apiIcingaClient->put('objects/hosts/' . $this->id, [
	                "templates" => [ $this->model->name ],
	                "attrs" => [
	                    'display_name' => $this->mixName,
	                    'address' => $this->mainIp->ip,
	                    'vars.device' => 'Radio',
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
	                    'vars.device' => 'Radio',
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
	            ['dhcp', 'boolean'],
	            ['dhcp', 'default', 'value' => false],
	            ['dhcp', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['monitoring', 'boolean'],
	            
	            ['geolocation', 'required', 'message' => 'Wartość nie może być pusta', 'when' => function($model) { return $model->monitoring; },
	               'whenClient' => "function(attribute, value) { return $('#gatewayvoip-monitoring').is(':checked') == true; }"
                ],
                ['geolocation', 'trim'],
                ['geolocation', 'match', 'pattern' => '/^[\d]{2}\.[\d]{7}, [\d]{2}\.[\d]{7}$/', 'message' => 'Niewłaściwy format (12.1234567, 12.1234567)'],
	            
	            [['serial', 'manufacturer_id', 'model_id', 'dhcp', 'monitoring'], 'safe'],
	        ]
		);
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['dhcp', 'monitoring', 'geolocation']);
	    $scenarios[self::SCENARIO_REPLACE] = ArrayHelper::merge($scenarios[self::SCENARIO_REPLACE], ['dhcp', 'monitoring', 'geolocation']);
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'dhcp' => 'DHCP',
	            'monitoring' => 'Monitorować',
	            'geolocation' => 'Geolokacja',
	        ]
	        );
	}
	
	function deleteFromTree() {
	    
	    parent::deleteFromTree();
	    $this->monitoring = false;
	}
	
	function replaceParams($destination, $post) {
	    
	    $destination->monitoring = $this->monitoring;
	    
	    $this->monitoring = false;
	}
}