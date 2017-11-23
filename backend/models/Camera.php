<?php

namespace backend\models;

use backend\models\Address;
use backend\models\Model;
use backend\models\Manufacturer;
use yii\helpers\ArrayHelper;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;

/**
 * This is the model class for table "switch".
 *
 * The followings are the available columns in table 'switch':
 * @property integer $id
 * @property integer $status
 * @property integer $virtual
 * @property string $name
 * @property integer $mac
 * @property string $serial
 * @property string $desc
 * @property integer $address
 * @property integer $type
 * @property integer $model
 * @property integer $manufacturer
 * @property integer $alias
 */

class Camera extends Device
{
	const TYPE = 6;
	
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
            	['alias', 'string', 'min' => 2, 'max' => 50],
            		
            	['mac', 'filter', 'filter' => function($value) { return strtolower($value); }],
            	['mac', 'string', 'min'=>12, 'max'=>17, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
//             	['mac', 'required', 'message'=>'Wartość wymagana'], //camera no required value
            	['mac', 'default', 'value' => NULL],
            	['mac', MacaddressValidator::className(), 'message'=>'Zły format'],
            	['mac', 'unique', 'targetClass' => 'backend\models\Device', 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
            		return strtolower($model->{$attribute}) !== strtolower($model->getOldAttribute($attribute));
            	}],
            	['mac', 'trim', 'skipOnEmpty' => true],
            	
            	['serial', 'filter', 'filter' => function($value) { return strtoupper($value); }],
            	['serial', 'string'],
            	['serial', 'unique', 'targetClass' => 'backend\models\Device', 'message'=>'Serial zajęty', 'when' => function ($model, $attribute) {
            		return $model->{$attribute} !== $model->getOldAttribute($attribute);
            	}],
            	['serial', 'default', 'value' => NULL],
            	['serial', 'required', 'message'=>'Wartość wymagana'],
            		
            	['manufacturer', 'integer'],
            	['manufacturer', 'required', 'message'=>'Wartość wymagana'],
            		
            	['model', 'integer'],
            	['model', 'required', 'message'=>'Wartość wymagana'],
                
                [['mac', 'serial', 'manufacturer', 'model', 'distribution'], 'safe'],
            ]
        );       
	}

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac', 'serial', 'manufacturer', 'model', 'alias']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'serial', 'alias']);
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
            	'alias' => 'Nazwa w monitoringu',
            ]
        ); 
	}
}
