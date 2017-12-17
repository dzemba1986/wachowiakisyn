<?php

namespace backend\models;

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
            	['alias', 'string', 'min' => 2, 'max' => 30],
                ['alias', 'required', 'message' => 'Wartość wymagana', 'when' => function ($model){ isset($model->status); }],
            		
            	['mac', 'filter', 'filter' => 'strtolower'],
            	['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort' => 'Za mało znaków', 'tooLong' => 'Za dużo znaków'],
            	['mac', 'required', 'message' => 'Wartość wymagana'],
            	['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
            	['mac', 'unique', 'targetClass' => 'backend\models\Device', 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
            		return strtolower($model->{$attribute}) !== strtolower($model->getOldAttribute($attribute));
            	}],
            	['mac', 'trim', 'skipOnEmpty' => true],
            	
            	['serial', 'filter', 'filter' => 'strtoupper'],
            	['serial', 'string', 'max' => 30],
            	['serial', 'unique', 'targetClass' => 'backend\models\Device', 'message' => 'Serial zajęty', 'when' => function ($model, $attribute) {
            		return $model->{$attribute} !== $model->getOldAttribute($attribute);
            	}],
            	['serial', 'required', 'message' => 'Wartość wymagana'],
            		
            	['manufacturer_id', 'integer'],
            	['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
            		
            	['model_id', 'integer'],
            	['model_id', 'required', 'message' => 'Wartość wymagana'],
                
                [['alias', 'mac', 'serial', 'manufacturer_id', 'model_id'], 'safe'],
            ]
        );       
	}

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac', 'serial', 'manufacturer_id', 'model_id']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'serial', 'alias']);
			
		return $scenarios;
	}
    
	public function attributeLabels()
	{
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'mac' => 'Mac',
            	'alias' => 'Nazwa w monitoringu',
                'selial' => 'Serial',
                'manufacturer_id' => 'Producent',
                'model_id' => 'Model',
            ]
        ); 
	}
	
    public function getIps(){

		return $this->hasMany(Ip::className(), ['device' => 'id'])->orderBy(['main' => SORT_DESC]);
	}
	
    public function getModel(){

		return $this->hasOne(Model::className(), ['id' => 'model_id']);
	}

    public function getManufacturer(){

		return $this->hasOne(Manufacturer::className(), ['id' => 'manufacturer_id']);
	}
}
