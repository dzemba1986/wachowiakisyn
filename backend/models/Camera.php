<?php

namespace backend\models;

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
		$this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes(){
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'alias',
	        ]
	        );
	}
	
	public static function find()
	{
		return new DeviceQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert)
	{
		if(!$insert) 
			$this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}
	
	public function rules(){
		
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['mac', 'required', 'message' => 'Wartość wymagana'],
                
                ['serial', 'required', 'message' => 'Wartość wymagana'],
                
                ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
                
                ['model_id', 'required', 'message' => 'Wartość wymagana'],
                
            	['alias', 'string', 'min' => 2, 'max' => 30],
                ['alias', 'required', 'message' => 'Wartość wymagana', 'when' => function ($model){ isset($model->status); }],
            		
                [['mac', 'serial', 'manufacturer_id', 'model_id', 'alias'], 'safe'],
            ]
        );       
	}

	public function scenarios(){
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE],['mac', 'serial', 'manufacturer_id', 'model_id']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'serial', 'alias']);
			
		return $scenarios;
	}
    
	public function attributeLabels()
	{
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
            	'alias' => 'Nazwa w monitoringu',
            ]
        ); 
	}
	
    public function getIps(){

		return $this->hasMany(Ip::className(), ['device' => 'id'])->orderBy(['main' => SORT_DESC]);
	}
}
