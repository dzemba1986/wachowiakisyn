<?php

namespace backend\modules\task\models;

use backend\models\Device;
use yii\helpers\ArrayHelper;
use backend\models\DeviceType;

/**
 * @property integer $id
 * @property string $create
 * @property string $close
 * @property string $description
 * @property integer $category_id
 * @property integer $type_id
 * @property integer $add_user
 * @property integer $close_user
 * @property integer $status
 * @property integer $address_id
 * @property integer $device_id
 * @property integer $device_type
 * @property integer $when
 * @property boolean $editable
 * @property integer $close_description
 */
class DeviceTask extends Task
{	
    public function attributes(){
    	
    	return ArrayHelper::merge(
    		parent::attributes(),
    		[
    			'when',	
    			'editable',
    			'close_description',
    			'device_id',
    			'device_type'	
    		]
    	);
    }

    public function rules(){
    	
        return ArrayHelper::merge(
        	[	
        		['address_id', 'default', 'value' => Device::findOne($this->device_id)->address_id],	
        			
        		['device_id', 'integer'],
        		['device_id', 'required', 'message' => 'Wartość wymagana'],
        			
        		['device_type', 'integer'],
        		['device_type', 'default', 'value' => Device::findOne($this->device_id)->type_id],	
        		['device_type', 'required', 'message' => 'Wartość wymagana'],
        			
        		['when', 'date', 'format' => 'yyyy-MM-dd H:i:s'],
        			
        		['close_description', 'required', 'message' => 'Wartość wymagana'],
        			
	            ['editable', 'boolean'],
	        	['editable', 'default', 'value' => true],
	            
        		[['editable', 'when', 'close_description', 'device_id', 'device_type', 'status'], 'safe']
        	],
        	parent::rules()
        );
    }
    
    public function scenarios()
    {
    	$scenarios = parent::scenarios();
    	array_push($scenarios[self::SCENARIO_CREATE], 'when', 'device_id', 'device_type');
    	array_push($scenarios[self::SCENARIO_UPDATE], 'when', 'status');
    	array_push($scenarios[self::SCENARIO_CLOSE], 'editable', 'close_description');
    	
    	return $scenarios;
    }
    
    public function attributeLabels()
    {
        return ArrayHelper::merge(
        	parent::attributeLabels(),	
        	[
	            'when' => 'Na kiedy',
	            'close_description' => 'Zrobiono',
        		'device_id' => 'Urządzenie',
        		'device_type' => 'Typ urządzenia'	
        	]
        );
    }
    
    public static function getCountOpenTask(){
    	
    	return self::find()->where(['and', ['status' => null], ['is not', 'device_id', null]])->count();
    }
    
    public function getDevice(){
    	
    	return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }
    
    public function getDeviceType(){
    	
    	return $this->hasOne(DeviceType::className(), ['id' => 'device_type']);
    }
}