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
 * @property string $start_date
 * @property Address $address
 * @property Type $type
 */

class Host extends Device{
    
	const TYPE = 5; //id w tabeli device_type dla Hosta
	
	public function init(){
		
	    $this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes(){
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'start_date',
	        ]
	    );
	}
	
	public static function find(){
		
	    return new DeviceQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert){
		
	    $this->type_id = self::TYPE;
		return parent::beforeSave($insert);
	}

	public function rules(){
		
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['mac', 'required', 'message' => 'Wartość wymagana'],
            	
                [['mac'], 'safe'],
            ]
        );       
	}
	
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac']);
			
		return $scenarios;
	}
    
	public function attributeLabels()
	{
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'start_date' => 'Konfiguracja',
            ]
        ); 
	}
	
	public function getIps(){
	    
	    return $this->hasMany(Ip::className(), ['device' => 'id'])->orderBy(['main' => SORT_DESC]);
	}
}
