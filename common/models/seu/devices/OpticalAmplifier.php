<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\OpticalAmplifierQuery;
use common\models\seu\devices\traits\Ip;
use common\models\seu\devices\traits\OneLink;
use common\models\seu\devices\traits\ParentDevice;
use yii\helpers\ArrayHelper;

class OpticalAmplifier extends BusinessDevice {
    
    use Ip, OneLink, ParentDevice;
    
	const TYPE = 13;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'input_power',
	            'output_power',
	            'insertion_loss'
	        ]
        );
	}
	
	public static function find() {
	    
		return new OpticalAmplifierQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
		if (!$insert)  $this->type_id = self::TYPE;
		
		return parent::beforeSave($insert);
	}
	
	public function rules() {
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['input_power', 'default', 'value' => function ($model, $attribute) {
	                return $model->model_id == 8 ? '-3 - 10' : '';
	            }],
	            ['input_power', 'string'],
	            ['input_power', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['output_power', 'default', 'value' => function ($model, $attribute) {
	                return $model->model_id == 8 ? 17 : 0;
	            }],
	            ['output_power', 'integer'],
	            ['output_power', 'required', 'message' => 'Wartość wymagana'],
	            
	            ['insertion_loss', 'default', 'value' => 0],
	            ['insertion_loss', 'integer'],
	            ['insertion_loss', 'required', 'message' => 'Wartość wymagana'],
	            
	            [['input_power', 'output_power'], 'safe'],
	        ]
		);
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['input_power', 'output_power', 'insertion_loss']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['input_level', 'output_power', 'insertion_loss']);
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'input_power' => 'Poziom mocy we.',
	            'output_power' => 'Poziom mocy wy.',
	            'insertion_loss' => 'Tłumienie'
	        ]
        );
	}
	
	public static function getCanSwitchToParentTypeIds() {
	    
	    return [OpticalTransmitter::TYPE, OpticalSplitter::TYPE, OpticalAmplifier::TYPE];
	}
}