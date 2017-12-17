<?php

namespace backend\models;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property boolean $status
 * @property string $name
 * @property string $proper_name
 * @property string $desc
 * @property integer $address_id
 * @property integer $type_id
 */

class Device extends ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_DELETE = 'delete';
	const SCENARIO_TOSTORE = 'toStore';
	const SCENARIO_TOTREE = 'toTree';
	
	public static function tableName()
	{
		return '{{device}}';
	}
	
	public function attributes(){
	    
	    return [
	        'id',
	        'status',
	        'name',
	        'proper_name',
	        'desc',
	        'address_id',
	        'type_id'
	    ];
	}
	
	public static function instantiate($row)
	{
		switch ($row['type']) {
			case Host::TYPE:
				return new Host() ;
			case Swith::TYPE:
				return new Swith() ;
			case Router::TYPE:
				return new Router();
			case Camera::TYPE:
				return new Camera();
			case GatewayVoip::TYPE:
				return new GatewayVoip();
			case MediaConverter::TYPE:
				return new MediaConverter();
			case Server::TYPE:
				return new Server();
			case Virtual::TYPE:
				return new Virtual();
			case Root::TYPE:
				return new Root();
			case Server::TYPE:
				return new Server();
			default:
				return new self;
		}
	}
	
	public function rules(){
		
		return [
            
            ['status', 'boolean'],
			['status', 'default', 'value' => null],
            ['status', 'required', 'message' => '{attribute} jest wymagany', 'on' => [self::SCENARIO_TOSTORE, self::SCENARIO_TOTREE]],
                      
            ['name', 'string', 'min' => 3, 'max' => 20],
		    
		    ['proper_name', 'string', 'min' => 3, 'max' => 30],
				
            ['desc', 'string'],
            
            ['address_id', 'integer'],
            ['address_id', 'required', 'on' => self::SCENARIO_TOTREE],
            
            ['type_id', 'integer'],
            ['type_id', 'required', 'message' => 'Wartość wymagana'],           
				
			[['status', 'desc', 'address_id', 'type_id', 'name', 'proper_name'], 'safe'],
		];
	}
    
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['desc', 'type_id'];
		$scenarios[self::SCENARIO_UPDATE] = ['name', 'desc', 'proper_name', 'address_id', 'status'];
			
		return $scenarios;
	}
	
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => 'Status',
            'name' => 'Nazwa',
			'proper_name' => 'Nazwa własna',	
            'desc' => 'Opis',
			'address_id' => 'Adres',
			'type_id' => 'Typ',
		);
	}
	
	public function getAddress(){
	    
	    return $this->hasOne(Address::className(), ['id' => 'address_id']);
	}
	
	public function getType(){
	    
	    return $this->hasOne(DeviceType::className(), ['id' => 'type_id']);
	}
}
