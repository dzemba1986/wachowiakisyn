<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\RouterQuery;
use common\models\seu\devices\traits\Ip;
use common\models\seu\devices\traits\ManyLinks;
use common\models\seu\devices\traits\ParentDevice;
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

class Router extends BusinessDevice {
    
    use Ip, ManyLinks, ParentDevice;
    
	const TYPE = 1;
	
	public function init() {
	    
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
	
	public static function find() {
	    
		return new RouterQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
		if(!$insert) 
			$this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}
    
	public function rules(){
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['monitoring', 'boolean'],
	            
	            ['geolocation', 'required', 'message' => 'Wartość nie może być pusta', 'when' => function($model) { return $model->monitoring; },
	               'whenClient' => "function(attribute, value) { return $('#gatewayvoip-monitoring').is(':checked') == true; }"
                ],
                ['geolocation', 'trim'],
                ['geolocation', 'match', 'pattern' => '/^[\d]{2}\.[\d]{7}, [\d]{2}\.[\d]{7}$/', 'message' => 'Niewłaściwy format (12.1234567, 12.1234567)'],
	        ]
		);
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['monitoring', 'geolocation']);
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
	
	function replaceParams($destination, $post) {
	    
	    $destination->monitoring = $this->monitoring;
	    $destination->geolocation = $this->geolocation;
	    
	    $this->monitoring = false;
	    $this->geolocation = null;
	}
	
}