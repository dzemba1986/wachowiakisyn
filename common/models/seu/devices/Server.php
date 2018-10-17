<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\ServerQuery;
use common\models\seu\devices\traits\Ip;
use common\models\seu\devices\traits\OneLink;
use yii\helpers\ArrayHelper;

/**
 * @property boolean $monitoring
 * @property string $geolocation
 */

class Server extends BusinessDevice {
    
    use Ip, OneLink;
    
	const TYPE = 4;
	
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
	    
		return new ServerQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
		if(!$insert) 
			$this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}
	
	public function rules() {
	    
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
}