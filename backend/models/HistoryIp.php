<?php

namespace backend\models;

/**
 * This is the model class for table "history_ip".
 *
 * The followings are the available columns in table 'history_ip':
 * @property string $ip
 * @property string $from_date
 * @property string $to_date
 * @property integer $address
 */

class HistoryIp extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'history_ip';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			['ip', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
			
			['from_date', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
				
			['to_date', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_UPDATE],
			['to_date', 'default', 'value' => NULL],
				
			['address', 'integer'],
			['address', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
				
			[['ip', 'from_date', 'to_date', 'address'], 'safe'],
		];
	}
	
	public function scenarios(){
		
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['ip', 'from_date', 'address'];
		$scenarios[self::SCENARIO_UPDATE] = ['to_date'];
		
		return $scenarios;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ip' => 'Adres IP',
			'from_date' => 'Data od',
			'to_date' => 'Data do',
			'address' => 'Adres'	
		);
	}
    
    public function getModelAddress(){
	
		return $this->hasOne(Address::className(), ['id' => 'address']);
	}
}
