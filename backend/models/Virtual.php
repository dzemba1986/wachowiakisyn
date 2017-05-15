<?php

namespace backend\models;

use backend\models\Address;
use backend\models\Model;
use backend\models\Manufacturer;
use yii\helpers\ArrayHelper;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;

/**
 * This is the model class for table "device".
 *
 * The followings are the available columns in table 'device':
 * @property integer $id
 * @property integer $status
 * @property integer $virtual
 * @property string $name
 * @property integer $mac
 * @property string $serial
 * @property string $desc
 * @property integer $address
 * @property integer $type

 */

class Virtual extends Device
{
	const TYPE = 7;
	
	public function init()
	{
		$this->type = self::TYPE;
		parent::init();
	}
	
	public static function find()
	{
		return new DeviceQuery(get_called_class(), ['type' => self::TYPE]);
	}
	
	public function beforeSave($insert)
	{
		if(!$insert) 
			$this->type = self::TYPE;
		return parent::beforeSave($insert);
	}
	
	public function rules(){
		
        return ArrayHelper::merge(
            parent::rules(),
            [
            	['mac', 'filter', 'filter' => function($value) { return strtolower($value); }],
            	['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
            	['mac', 'default', 'value' => null],
            	['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
            	['mac', 'unique', 'targetClass' => 'backend\models\Device', 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
            		return strtolower($model->{$attribute}) !== strtolower($model->getOldAttribute($attribute));
            	}],
            	['mac', 'trim', 'skipOnEmpty' => true],
            	
            	['serial', 'default', 'value' => NULL],
            		
            	['manufacturer', 'default', 'value' => 10],

            	['model', 'default', 'value' => 14],           		
                
                [['mac'], 'safe'],
            ]
        );       
	}

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac', 'manufacturer', 'model']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac']);
		$scenarios[self::SCENARIO_TOSTORE] = ArrayHelper::merge($scenarios[self::SCENARIO_TOSTORE], ['address', 'status']);
		$scenarios[self::SCENARIO_TOTREE] = ArrayHelper::merge($scenarios[self::SCENARIO_TOTREE], ['address', 'status']);
		//$scenarios[self::SCENARIO_DELETE] = ['close_date', 'close_user'];
			
		return $scenarios;
	}
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
    
	public function attributeLabels()
	{
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'distribution' => 'Rodzaj',
            ]
        ); 
	}
}
