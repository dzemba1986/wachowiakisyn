<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\OpticalSplitterQuery;
use common\models\seu\devices\traits\OneLink;
use common\models\seu\devices\traits\ParentDevice;
use yii\helpers\ArrayHelper;

class OpticalSplitter extends BusinessDevice
{
    use OneLink, ParentDevice;
    
	const TYPE = 14;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'insertion_loss',
	        ]
        );
	}
	
	public static function find() {
	    
		return new OpticalSplitterQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
		if (!$insert)  $this->type_id = self::TYPE;
		
		return parent::beforeSave($insert);
	}
	
	public function rules() {
	    
	    $parent = parent::rules();
	    unset($parent['mac_required']);
	    
	    return ArrayHelper::merge(
	        $parent,
	        [
	            ['insertion_loss', 'default', 'value' => function ($model, $attribute) {
	                if ($model->model_id == 36 || $model->model_id == 89 || $model->model_id == 106) $out = 3.8;
	                if ($model->model_id == 37 || $model->model_id == 97) $out = 7.2;
	                if ($model->model_id == 38 || $model->model_id == 82) $out = 10.2;
	                if ($model->model_id == 39 || $model->model_id == 88) $out = 13.6;
	                if ($model->model_id == 44) $out = 16.8;
	                if ($model->model_id == 65) $out = 20.9;
	                
	                return $out;
	            }],
	            ['insertion_loss', 'number'],
	            ['insertion_loss', 'required', 'message' => 'Wartość wymagana'],
	            
	            [['insertion_loss'], 'safe'],
	        ]
		);
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['insertion_loss']);
	    $scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['insertion_loss']);
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
	    return ArrayHelper::merge(
	        parent::attributeLabels(),
	        [
	            'insertion_loss' => 'Tłumienie',
	        ]
        );
	}
	
	public static function getCanSwitchToParentTypeIds() {
	    
	    return [OpticalTransmitter::TYPE, OpticalSplitter::TYPE, OpticalAmplifier::TYPE];
	}
}