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
 * @property string $mac
 * @property string $serial
 * @property integer $model_id
 * @property integer $manufacturer_id
 * @property backend\models\Address $address
 * @property backend\models\Type $type
 * @property backend\models\Manufacturer $manufacturer
 * @property backend\models\Model $model
 * @property Ip[] $ips
 * @property Tree[] $links
 * @property string $combinedName 
 */

class Device extends ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	
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
				return new Host();
			case Swith::TYPE:
				return new Swith();
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
	
	public function rules() {
		
		return [
            
            ['status', 'boolean'],
			['status', 'default', 'value' => null],
            ['status', 'required', 'message' => 'Wartość wymagana', 'on' => [self::SCENARIO_UPDATE]],
                      
		    ['name', 'string', 'min' => 3, 'max' => 30, 'tooShort' => 'Za mało znaków', 'tooLong' => 'Za dużo znaków'],
		    
		    ['proper_name', 'string', 'min' => 2, 'max' => 15, 'tooShort' => 'Za mało znaków', 'tooLong' => 'Za dużo znaków'],
		    ['proper_name', 'match', 'pattern' => '/^([a-zA-Z]|\d){1}([a-zA-Z]|\d|\.)+[a-zA-Z|\d]{1}$/', 'message' => 'Niewłaściwy format'],
		    ['proper_name', 'trim', 'skipOnEmpty' => true],
		    ['proper_name', 'default', 'value' => null],
				
		    ['desc', 'string', 'max' => 1000],
		    
		    ['mac', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => true],
		    ['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort' => 'Za mało znaków', 'tooLong' => 'Za dużo znaków'],
		    ['mac', 'unique', 'targetClass' => static::className(), 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
		        return strtolower($model->mac) !== strtolower($model->getOldAttribute('mac'));
		    }],
		    ['mac', 'trim', 'skipOnEmpty' => true],
		    
		    ['serial', 'filter', 'filter' => 'strtoupper'],
		    ['serial', 'string', 'max' => 30],
		    ['serial', 'unique', 'targetClass' => 'backend\models\Device', 'message' => 'Serial zajęty', 'when' => function ($model, $attribute) {
		        return $model->{$attribute} !== $model->getOldAttribute($attribute);
		    }],
		    ['serial', 'trim'],
		    
		    ['manufacturer_id', 'integer'],
		    
		    ['model_id', 'integer'],
            
            ['address_id', 'integer'],
            ['address_id', 'required'],
            
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
	
	public function getIps(){
	    
	    return $this->hasMany(Ip::className(), ['device_id' => 'id'])->orderBy(['main' => SORT_DESC]);
	}
	
	public function getLinks(){
	    
	    return $this->hasMany(Tree::className(), ['device' => 'id']);
	}
	
	function getMixName() {
	    
	    return $this->proper_name ? $this->type->prefix . $this->name . '_' . $this->proper_name : $this->type->prefix . $this->name;
	}
	
	public function getParentPortName() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentPortIndex = $this->links[0]->parent_port;
	    
	    return $parentDevice->model->port[$parentPortIndex];
	}
	
	public function getParentIp() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    
	    if (!empty($parentDevice->ips)) return $parentDevice->ips[0]->ip;
	    else return 'Brak ip';
	}
	
	function isParent() {
	    
	    return Tree::find()->where(['parent_device' => $this->id])->count() > 0 ? true : false;
	}
	
	public static function create($typeId) {
	    switch($typeId) {
	        case Swith::TYPE:
	            return new Swith();
	            break;
	        case Router::TYPE:
	            return new Router();
	            break;
	        case GatewayVoip::TYPE:
	            return new GatewayVoip();
	            break;
	        case Camera::TYPE:
	            return new Camera();
	            break;
	        case MediaConverter::TYPE:
	            return new MediaConverter();
	            break;
	        case Server::TYPE:
	            return new Server();
	            break;
	        case Virtual::TYPE:
	            return new Virtual();
	            break;
	        default :
	            return new Device();
	    }
	}
}