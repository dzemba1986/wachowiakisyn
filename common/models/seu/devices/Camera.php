<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\CameraQuery;
use common\models\seu\devices\traits\Config;
use common\models\seu\devices\traits\Ip;
use common\models\seu\devices\traits\OneLink;
use common\models\seu\network\Dhcp;
use yii\helpers\ArrayHelper;

/**
 * @property string $mac
 * @property string $serial
 * @property integer $alias
 * @property boolean $dhcp
 * @property boolean $monitoring
 * @property boolean $geolocation
 */

class Camera extends BusinessDevice { 
    
    use Ip, OneLink, Config;
    
	const TYPE = 6;
	private $conf;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'mac',
	            'serial',
	            'alias',
	            'dhcp',
	            'monitoring',
	            'geolocation',
	        ]
        );
	}
	
	public static function find() {
	    
		return new CameraQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
	    if (!YII_DEBUG && !$insert) {
	        if (array_key_exists('monitoring', $this->dirtyAttributes) && !$this->oldAttributes['monitoring'] && $this->monitoring) {
	            
	            \Yii::$app->apiIcingaClient->put('objects/hosts/' . $this->id, [
	                "templates" => [ $this->model->name ],
	                "attrs" => [
	                    'display_name' => $this->mixName,
	                    'address' => $this->firstIp,
	                    'vars.geolocation' => $this->geolocation,
	                    'vars.device' => 'Camera',
	                    'vars.model' => $this->modelName,
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
                        'address' => $this->firstIp,
                        'vars.geolocation' => $this->geolocation,
                        'vars.device' => 'Camera',
                        'vars.model' => $this->modelName,
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
		
	    $parent = parent::rules();
	    unset($parent['mac_unique']);
        return ArrayHelper::merge(
            $parent,
            [
                ['mac', 'unique', 'targetClass' => static::className(), 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
                return strtolower($model->{$attribute}) !== strtolower($model->getOldAttribute($attribute));
                }, 'filter' => ['status' => true], 'on' => [self::SCENARIO_CREATE, self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
                
            	['alias', 'string', 'min' => 2, 'max' => 30],
                ['alias', 'required', 'message' => 'Wartość wymagana', 'when' => function ($model){ isset($model->status); }],
                
                ['dhcp', 'boolean', 'trueValue' => true, 'falseValue' => false],
                ['dhcp', 'default', 'value' => false],
                ['dhcp', 'required', 'message' => 'Wartość wymagana'],
                ['dhcp', 'filter', 'filter' => 'boolval'],
                
                ['monitoring', 'boolean', 'trueValue' => true, 'falseValue' => false],
                ['monitoring', 'filter', 'filter' => 'boolval'],
                
                
                ['geolocation', 'required', 'message' => 'Wartość nie może być pusta', 'when' => function($model) { return $model->monitoring; },
                    'whenClient' => "function(attribute, value) { return $('#camera-monitoring').is(':checked') == true; }"
                ],
                ['geolocation', 'trim'],
                ['geolocation', 'match', 'pattern' => '/^[\d]{2}\.[\d]{7}, [\d]{2}\.[\d]{7}$/', 'message' => 'Niewłaściwy format (12.1234567, 12.1234567)'],
            ]
        );       
	}

	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['alias', 'dhcp', 'monitoring', 'geolocation']);
		$scenarios[self::SCENARIO_REPLACE] = ArrayHelper::merge($scenarios[self::SCENARIO_REPLACE], ['mac', 'alias', 'dhcp', 'monitoring', 'geolocation']);
			
		return $scenarios;
	}
    
	public function attributeLabels() {
	    
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
            	'alias' => 'Alias',
                'geolocation' => 'Geolokacja',
                'monitoring' => 'Monitorować',
            ]
        ); 
	}
	
	function afterSave($insert, $changedAttributes) {
	    
	    if (!$insert) {
	        if (isset($changedAttributes['mac']) || isset($changedAttributes['dhcp'])) {
	            !empty($this->ips) ? Dhcp::generateFile($this->ips[0]->subnet) : null;
	        }
	    }
	}
	
	function addOnTree() {
	    
	    parent::addOnTree();
	    $this->dhcp = true;
	}
	
	function deleteFromTree() {
	    
	    parent::deleteFromTree();
	    $this->alias = null;
	    $this->monitoring = false;
	    $this->geolocation = null;
	    $this->dhcp = null;
	}
	
	function replaceParams($destination, $post) {
	    
	    $destination->monitoring = $this->monitoring;
	    $destination->geolocation = $this->geolocation;
	    $destination->dhcp = $this->dhcp;
	    $destination->alias = $this->alias;
	    
	    $this->monitoring = false;
	    $this->geolocation = null;
	    $this->dhcp = false;
	    $this->alias = null;
	    
	    if ($post['leaveMac']) {
            $tempMac = $destination->mac;
            $destination->mac = $this->mac;
            $this->mac = $tempMac;
        }
	}
}
