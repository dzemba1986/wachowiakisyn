<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;
use common\models\seu\devices\Device;
use yii\behaviors\AttributeBehavior;
use yii\helpers\ArrayHelper;

/**
 * @property integer $device_id
 */

class DeviceTask extends Task {
    
    const TYPE = 1;

    public static function columns() {
        
        return ArrayHelper::merge(
            parent::columns(),
            [
                'device_id',
            ]
        );
    }
    
    public function rules() {
    	
        return ArrayHelper::merge(
        	[	
        	    ['category_id', 'required', 'message' => 'Wartość wymagana'],
        	    ['category_id', 'in', 'range' => [1, 2, 3, 4]],
        			
        		['device_id', 'integer'],
        		['device_id', 'required', 'message' => 'Wartość wymagana'],
        			
        		['close_desc', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],
        	],
        	parent::rules()
        );
    }
    
    public function scenarios() {
        
    	$scenarios = parent::scenarios();
    	array_push($scenarios[self::SCENARIO_CREATE], 'device_id');
    	
    	return $scenarios;
    }
    
    public function attributeLabels() {
        
        return ArrayHelper::merge(
        	parent::attributeLabels(),	
        	[
        		'device_id' => 'Urządzenie',
        	]
        );
    }
    
    public function behaviors() {
        
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                [
                    'class' => AttributeBehavior::class,
                    'attributes' => [
                        self::EVENT_BEFORE_INSERT => 'address_id',
                    ],
                    'value' => Device::find()->select('address_id')->where(['id' => $this->device_id])->asArray()->one()['address_id'],
                ],
            ],
        );
    }
    
//     public function beforeSave($insert) {
        
//         if (!$insert) {
//             if ($this->status != 2 && !$this->close_at) $this->status = 2;
//         }
        
//         parent::beforeSave($insert);
//     }
    
    public static function find() {
        
        return new TaskQuery(get_called_class(), ['type_id' => self::TYPE, 'columns' => self::columns()]);
    }
    
    public function getDevice() {
    	
    	return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }
}