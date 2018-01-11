<?php

namespace backend\models;

use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\db\ActiveRecord;

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
 * @property Address $address
 * @property Type $type
 * @property Manufacturer $manufacturer
 * @property Model $model
 */

class Device extends ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_DELETE = 'delete';
	
	public static function tableName(){
	    
		return '{{device}}';
	}
	
	public function attributes(){
	    
	    return [
	        'id',
	        'status',
	        'name',
	        'proper_name',
	        'desc',
	        'mac',
	        'serial',
	        'address_id',
	        'type_id',
	        'manufacturer_id',
	        'model_id',
	    ];
	}
	
	public static function instantiate($row){
	    
		switch ($row['type_id']) {
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
            ['status', 'required', 'message' => 'Wartość wymagana', 'on' => [self::SCENARIO_UPDATE]],
                      
            ['name', 'string', 'min' => 3, 'max' => 20],
		    
		    ['proper_name', 'string', 'min' => 3, 'max' => 30],
				
		    ['desc', 'string', 'max' => 1000],
		    
		    ['mac', 'filter', 'filter' => 'strtolower'],
		    ['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort' => 'Za mało znaków', 'tooLong' => 'Za dużo znaków'],
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
		    
		    ['manufacturer_id', 'integer'],
		    
		    ['model_id', 'integer'],
            
            ['address_id', 'integer'],
            ['address_id', 'required'], //TODO adres nie jest wymagany w magazynie
            
            ['type_id', 'integer'],
            ['type_id', 'required', 'message' => 'Wartość wymagana'],           
				
			[['status', 'desc', 'address_id', 'type_id', 'name', 'proper_name'], 'safe'],
		];
	}
    
	public function scenarios(){
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['desc', 'type_id'];
		$scenarios[self::SCENARIO_UPDATE] = ['name', 'desc', 'proper_name', 'address_id', 'status'];
			
		return $scenarios;
	}
	
	public function attributeLabels(){
	    
		return array(
			'id' => 'ID',
			'status' => 'Status',
            'name' => 'Nazwa',
			'proper_name' => 'Nazwa własna',	
            'desc' => 'Opis',
		    'mac' => 'Mac',
		    'selial' => 'Serial',
			'address_id' => 'Adres',
			'type_id' => 'Typ',
		    'manufacturer_id' => 'Producent',
		    'model_id' => 'Model',
		);
	}
	
	public function getAddress(){
	    
	    return $this->hasOne(Address::className(), ['id' => 'address_id']);
	}
	
	public function getType(){
	    
	    return $this->hasOne(DeviceType::className(), ['id' => 'type_id']);
	}
	
	public function getModel(){
	    
	    return $this->hasOne(Model::className(), ['id' => 'model_id']);
	}
	
	public function getManufacturer(){
	    
	    return $this->hasOne(Manufacturer::className(), ['id' => 'manufacturer_id']);
	}
	
	public function getLink(){
	    
	    return $this->hasMany(Tree::className(), ['device' => 'id']);
	}
}
