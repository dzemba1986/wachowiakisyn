<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\SwithQuery;
use common\models\seu\devices\traits\Config;
use common\models\seu\devices\traits\Ip;
use common\models\seu\devices\traits\ManyLinks;
use common\models\seu\devices\traits\ParentDevice;
use yii\helpers\ArrayHelper;

/**
 * @property boolean $distribution
 * @property boolean $monitoring
 * @property string $geolocation
 */

class Swith extends BusinessDevice {
    
    use Ip, ManyLinks, Config, ParentDevice;
    
	const TYPE = 2;
	private $configType = NULL;
	private $snmpDesc = NULL;
	private $snmpVlan = NULL;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'distribution',
	            'monitoring',
	            'geolocation',
	        ]
	    );
	}
	
	public static function find() {
	    
		return new SwithQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
	    if (!YII_DEBUG && !$insert) {
	        if (array_key_exists('monitoring', $this->dirtyAttributes) && !$this->oldAttributes['monitoring'] && $this->monitoring) {
	                    
                \Yii::$app->apiIcingaClient->put('objects/hosts/' . $this->id, [
                    "templates" => [ $this->model->name ],
                    "attrs" => [
                        'display_name' => $this->mixName,
                        'address' => $this->mainIp->ip,
                        'vars.geolocation' => $this->geolocation,
                        'vars.device' => 'Switch',
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
	        
	        if ((array_key_exists('geolocation', $this->dirtyAttributes) || array_key_exists('name', $this->dirtyAttributes) || array_key_exists('proper_name', $this->dirtyAttributes)) && $this->monitoring) {
	            
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
	                    'vars.geolocation' => $this->geolocation,
	                    'vars.device' => 'Switch',
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
	    
		if(!$insert) 
			$this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}
	
	public function rules() {
		
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['distribution', 'boolean'],
                ['distribution', 'required', 'message' => 'Wartość wymagana', 'when' => function ($model){ isset($model->status); }],
                
                ['monitoring', 'boolean'],
                
                ['geolocation', 'required', 'message' => 'Wartość nie może być pusta', 'when' => function($model) { return $model->monitoring; }, 
                    'whenClient' => "function(attribute, value) { return $('#swith-monitoring').is(':checked') == true; }"
                ],
                ['geolocation', 'trim'],
                ['geolocation', 'match', 'pattern' => '/^[\d]{2}\.[\d]{7}, [\d]{2}\.[\d]{7}$/', 'message' => 'Niewłaściwy format (12.1234567, 12.1234567)'],
            ]
        );       
	}

	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['distribution', 'monitoring', 'geolocation']);
	    $scenarios[self::SCENARIO_REPLACE] = ArrayHelper::merge($scenarios[self::SCENARIO_REPLACE], ['distribution', 'monitoring', 'geolocation']);
	    
	    return $scenarios;
	}

	public function attributeLabels() {
	    
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'distribution' => 'Szkieletowy',
                'geolocation' => 'Geolokacja',
                'monitoring' => 'Monitorować'
            ]
        ); 
	}
	
	public function getConfigType() {
	    
	    if (is_null($this->configType)) $this->configType = $this->getModel()->select('config')->asArray()->one()['config'];
	    
	    return $this->configType;
	}
	
	public function deleteFromTree() {
	    
	    parent::deleteFromTree();
	    $this->monitoring = false;
	    $this->geolocation = null;
	}
	
	function replaceParams($destination, $post) {
	    
	    $destination->monitoring = $this->monitoring;
	    $destination->geolocation = $this->geolocation;
	    
	    $this->monitoring = false;
	    $this->geolocation = null;
	}
	
	public function getSnmpDesc() : string {

	    if (is_null($this->snmpDesc)) {
	        if ($this->getConfigType() == 1) $this->snmpDesc = substr(snmpget($this->getFirstIp(), 'wymyslj@k12spr0st3', '1.3.6.1.2.1.31.1.1.1.18.' . $this->getPortNumber()), 7);
	        elseif ($this->getConfigType() == 2) $this->snmpDesc = substr(snmpget($this->getFirstIp(), 'wymyslj@k12spr0st3', '1.0.8802.1.1.2.1.3.7.1.4.' . $this->getPortNumber()), 7);
	        else $this->snmpDesc = 'Brak opisu';
	    }
	    
	    return $this->snmpDesc;
	}
	
	public function getSnmpVlan() : string {
	    
	    if (is_null($this->snmpVlan)) $this->snmpVlan = substr(snmpget($this->firstIp, 'wymyslj@k12spr0st3', '1.3.6.1.2.1.17.7.1.4.5.1.1.' . $this->portIndex), 9);
	    
	    return $this->snmpVlan;
	}
}