<?php

namespace backend\models;

/**
 * @property string $ip
 * @property string $from_date
 * @property string $to_date
 * @property integer $address_id
 */

class HistoryIp extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE_IP = 'create-ip';
	const SCENARIO_DELETE_IP = 'delete-ip';
	
	public static function tableName()
	{
		return '{{history_ip}}';
	}

	public function rules()
	{
		return [
			['ip', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE_IP],
			
			['from_date', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE_IP],
				
			['to_date', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_DELETE_IP],
			['to_date', 'default', 'value' => null],
				
			['address_id', 'integer'],
			['address_id', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE_IP],
				
			[['ip', 'from_date', 'to_date', 'address_id'], 'safe'],
		];
	}
	
	public function scenarios(){
		
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE_IP] = ['ip', 'from_date', 'address_id'];
		$scenarios[self::SCENARIO_DELETE_IP] = ['to_date'];
		
		return $scenarios;
	}
	
	public function attributeLabels()
	{
		return array(
			'ip' => 'Adres IP',
			'from_date' => 'Data od',
			'to_date' => 'Data do',
			'address_id' => 'Adres'	
		);
	}
    
    public function getAddress(){
	
		return $this->hasOne(Address::className(), ['id' => 'address_id']);
	}
}