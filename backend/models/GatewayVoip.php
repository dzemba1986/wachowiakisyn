<?php

namespace backend\models;

use backend\models\configuration\ECSeriesConfiguration;
use backend\models\configuration\GSSeriesConfiguration;
use backend\models\configuration\XSeriesConfiguration;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\helpers\ArrayHelper;

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
 */

class GatewayVoip extends Device
{
	const TYPE = 3;
	private $conf;
	
	public function init()
	{
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'monitoring',
	            'geolocation',
	        ]
        );
	}
	
	public static function find()
	{
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
	                    'vars.device' => 'Voip',
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
	            
	            \Yii::$app->apiIcingaClient->post('objects/hosts/' . $this->id, [
	                "attrs" => [
	                    'vars.display_name' => $this->mixName,
	                    'vars.geolocation' => $this->geolocation,
	                ]
	            ], [
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
	
	public function rules(){
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['mac', 'required', 'message' => 'Wartość wymagana'],
	            ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
	            
	            ['serial', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['model_id', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['monitoring', 'boolean'],
	            
	            ['geolocation', 'required', 'message' => 'Wartość nie może być pusta', 'when' => function($model) { return $model->monitoring; },
	               'whenClient' => "function(attribute, value) { return $('#gatewayvoip-monitoring').is(':checked') == true; }"
                ],
                ['geolocation', 'trim'],
                ['geolocation', 'match', 'pattern' => '/^[\d]{2}\.[\d]{7}, [\d]{2}\.[\d]{7}$/', 'message' => 'Niewłaściwy format (12.1234567, 12.1234567)'],
	            
                [['mac', 'serial', 'manufacturer_id', 'model_id', 'monitoring', 'geolocation'], 'safe'],
	        ]
	    );
	}
	
	public function scenarios(){
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE],['mac', 'serial', 'manufacturer_id', 'model_id']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'serial', 'monitoring', 'geolocation']);
	    $scenarios[self::SCENARIO_REPLACE] = ArrayHelper::merge($scenarios[self::SCENARIO_REPLACE], ['monitoring', 'geolocation']);
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'geolocation' => 'Geolokacja',
	            'monitoring' => 'Monitorować',
	        ]
	        );
	}
	
	public function configurationAdd() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
	        if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->add();
	}
	
	public function configurationDrop() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
	        if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->drop();
	}
	
	public function configurationChangeMac($newMac) {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
	        if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
	        elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
	        else return ' ';
	    } else return ' ';
	    
	    return $this->conf->changeMac($newMac);
	}
}
