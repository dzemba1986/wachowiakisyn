<?php

namespace backend\models;

use backend\models\configuration\ECSeriesConfiguration;
use backend\models\configuration\GSSeriesConfiguration;
use backend\models\configuration\XSeriesConfiguration;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @property integer $id
 * @property boolean $status
 * @property string $name
 * @property string $proper_name
 * @property string $desc
 * @property integer $address_id
 * @property integer $type_id
 * @property integer $mac
 * @property string $serial
 * @property integer $model_id
 * @property integer $manufacturer_id
 * @property integer $alias
 * @property boolean $dhcp
 * @property boolean $monitoring
 * @property boolean $geolocation
 */

class Camera extends Device
{
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
	            'alias',
	            'dhcp',
	            'monitoring',
	            'geolocation',
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
	                    'vars.geolocation' => $this->geolocation,
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
	    
		if(!$insert) 
			$this->type_id = self::TYPE;
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
                
            	['alias', 'string', 'min' => 2, 'max' => 30],
                ['alias', 'required', 'message' => 'Wartość wymagana', 'when' => function ($model){ isset($model->status); }],
                
                ['dhcp', 'boolean'],
                ['dhcp', 'default', 'value' => false],
                ['dhcp', 'required', 'message' => 'Wartość wymagana'],
                
                ['monitoring', 'boolean'],
                
                ['geolocation', 'required', 'message' => 'Wartość nie może być pusta', 'when' => function($model) { return $model->monitoring; },
                    'whenClient' => "function(attribute, value) { return $('#camera-monitoring').is(':checked') == true; }"
                ],
                ['geolocation', 'trim'],
                ['geolocation', 'match', 'pattern' => '/^[\d]{2}\.[\d]{7}, [\d]{2}\.[\d]{7}$/', 'message' => 'Niewłaściwy format (12.1234567, 12.1234567)'],
            		
                [['mac', 'serial', 'manufacturer_id', 'model_id', 'alias', 'dhcp', 'monitoring', 'geolocation'], 'safe'],
            ]
        );       
	}

	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE],['mac', 'serial', 'manufacturer_id', 'model_id']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'serial', 'alias', 'dhcp', 'monitoring', 'geolocation']);
		$scenarios[self::SCENARIO_REPLACE] = ArrayHelper::merge($scenarios[self::SCENARIO_REPLACE], ['alias', 'dhcp', 'monitoring', 'geolocation']);
			
		return $scenarios;
	}
    
	public function attributeLabels() {
	    
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
            	'alias' => 'Nazwa w monitoringu',
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
	
	function replace($destination) {
	    
	    parent::replace($destination);
	    $destination->monitoring = $this->monitoring;
	    $destination->geolocation = $this->geolocation;
	    $destination->dhcp = $this->dhcp;
	    $destination->alias = $this->alias;
	    
	    $this->monitoring = false;
	    $this->geolocation = null;
	    $this->dhcp = false;
	    $this->alias = null;
	}
	
	public function configurationAdd() {
	    
	    if (!empty($this->ips)) {
	        if ($this->parentConfigType == 1) $this->conf = new GSSeriesConfiguration($this);
	        elseif ($this->parentConfigType == 2) $this->conf = new XSeriesConfiguration($this);
	        elseif ($this->parentConfigType == 5) $this->conf = new ECSeriesConfiguration($this);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->add();
	}
	
	public function configurationDrop($auto = false) {
	    
	    if (!empty($this->ips)) {
	        if ($this->parentConfigType == 1) $this->conf = new GSSeriesConfiguration($this);
	        elseif ($this->parentConfigType == 2) $this->conf = new XSeriesConfiguration($this);
	        elseif ($this->parentConfigType == 5) $this->conf = new ECSeriesConfiguration($this);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->drop($auto);
	}
	
	public function configurationChangeMac($newMac) {
	    
	    if (!empty($this->ips)) {
	        if ($this->parentConfigType == 1) $this->conf = new GSSeriesConfiguration($this);
	        elseif ($this->parentConfigType == 2) $this->conf = new XSeriesConfiguration($this);
	        elseif ($this->parentConfigType == 5) $this->conf = new ECSeriesConfiguration($this);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->changeMac($newMac);
	}
	
	function getUrlImage() {
	    
	    return Html::a('Podgląd', 'http://' . $this->getMainIp()->one()->ip . '/image', ['target'=>'_blank']);
	}
	
	function getUrlReboot() {
	    
	    return Html::a('Reboot', 'http://' . $this->getMainIp()->one()->ip . '/command/main.cgi?System=reboot', ['target'=>'_blank']);
	}
}
