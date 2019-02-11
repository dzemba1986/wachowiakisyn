<?php

namespace common\models\seu\devices;

use common\models\seu\devices\query\OpticalTerminatorQuery;
use common\models\seu\devices\traits\OneLink;
use common\models\seu\devices\traits\ParentDevice;
use yii\helpers\ArrayHelper;

class OpticalTerminator extends BusinessDevice
{
    use OneLink, ParentDevice;
    
	const TYPE = 15;
	
	public function init() {
	    
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public static function find() {
	    
		return new OpticalTerminatorQuery(get_called_class(), ['type_id' => self::TYPE]);
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
	        []
		);
	}
		
	public static function getCanSwitchToParentTypeIds() {
	    
	    return [OpticalTransmitter::TYPE, OpticalSplitter::TYPE, OpticalAmplifier::TYPE];
	}
}