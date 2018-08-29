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

abstract class Device extends ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_REPLACE = 'replace';
	const ONE_TO_ONE_DEVICE = [3,6,7,10,11];
	
	public static function tableName() {
	    
		return '{{device}}';
	}
	
	public function attributes() {
	    
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
	
	public static function instantiate($row) {
	    
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
			case Ups::TYPE:
			    return new Ups();
			case Radio::TYPE:
			    return new Radio();
		}
	}
	
	public function rules() {
		
		return [
            
            ['status', 'boolean'],
			['status', 'default', 'value' => null],
		    ['status', 'required', 'message' => 'Wartość wymagana', 'on' => [self::SCENARIO_UPDATE], 'when' => function ($model) { return $model->address_id <> 1; }],
                      
		    ['name', 'string', 'min' => 3, 'max' => 40, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
		    
		    ['proper_name', 'string', 'min' => 2, 'max' => 15, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
		    ['proper_name', 'match', 'pattern' => '/^[a-zA-ZżźćńółęąśŻŹĆĄŚĘŁÓŃ|\d]{1}[a-zA-ZżźćńółęąśŻŹĆĄŚĘŁÓŃ|\d|\.]+[a-zA-ZżźćńółęąśŻŹĆĄŚĘŁÓŃ|\d]{1}$/', 'message' => 'Niewłaściwy format'],
		    ['proper_name', 'trim', 'skipOnEmpty' => true],
		    ['proper_name', 'default', 'value' => null],
				
		    ['desc', 'string', 'max' => 1000],
		    
		    ['mac', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => true],
		    ['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
		    ['mac', 'unique', 'targetClass' => static::className(), 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
		        return strtolower($model->{$attribute}) !== strtolower($model->getOldAttribute($attribute));
		    }, 'filter' => ['status' => true], 'on' => [self::SCENARIO_CREATE, self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
		    ['mac', 'trim', 'skipOnEmpty' => true],
		    
		    ['serial', 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],
		    ['serial', 'string', 'max' => 30, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
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
    
	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['desc', 'type_id'];
		$scenarios[self::SCENARIO_UPDATE] = ['name', 'desc', 'proper_name', 'address_id', 'status'];
		$scenarios[self::SCENARIO_REPLACE] = ['status', 'name', 'mac', 'address_id', 'proper_name'];
			
		return $scenarios;
	}
	
	public function attributeLabels() {
	    
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
	
	public function getAddress() {
	    
	    return $this->hasOne(Address::className(), ['id' => 'address_id']);
	}
	
	public function getType() {
	    
	    return $this->hasOne(DeviceType::className(), ['id' => 'type_id']);
	}
	
	public function getModel() {
	    
	    return $this->hasOne(Model::className(), ['id' => 'model_id']);
	}
	
	public function getManufacturer() {
	    
	    return $this->hasOne(Manufacturer::className(), ['id' => 'manufacturer_id']);
	}
	
	public function getIps(){
	    
	    return $this->hasMany(Ip::className(), ['device_id' => 'id'])->orderBy(['main' => SORT_DESC]);
	}
	
	public function getMainIp(){
	    
	    return $this->hasOne(Ip::className(), ['device_id' => 'id'])->where(['main' => true]);
	}
	
	public function getLinks(){
	    
	    return $this->hasMany(Tree::className(), ['device' => 'id']);
	}
	
	function getMixName($pl = true) : string {
	    
	    if ($pl)
	        return $this->proper_name ? $this->type->prefix . $this->name . '_' . $this->proper_name : $this->type->prefix . $this->name;
        else {
            $trans = ['Ą' => 'A', 'Ł' => 'L', 'Ę' => 'E', 'Ó' => 'O', 'Ś' => 'S', 'Ć' => 'C', 'Ż' => 'Z', 'Ź' => 'Z', 'Ń' => 'N', 'ą' => 'a', 'ł' => 'l', 'ę' => 'e', 'ó' => 'o', 'ś' => 's', 'ć' => 'c', 'ż' => 'z', 'ź' => 'z', 'ń' => 'n'];
	        return $this->proper_name ? $this->type->prefix . strtr($this->name,  $trans) . '_' . strtr($this->proper_name, $trans) : $this->type->prefix . strtr($this->name, $trans);
        }
    }
    
    function addOnTree() {
        
        $this->status = true;
    }
    
    function deleteFromTree() {
        
        $this->address_id = 1;
        $this->status = null;
        $this->name = null;
        $this->proper_name = null;
    }
    
    function replace($destination) {
        
        $destination->address_id = $this->address_id;
        $destination->status = $this->status;
        $destination->name = $this->name;
        $destination->proper_name = $this->proper_name;
        
        $this->address_id = 1;
        $this->status = null;
        $this->name = null;
        $this->proper_name = null;
    }
    
    public function getParentPortIndex() : int {
        
        $parentId = $this->links[0]->parent_device;
        $parentModelId = Device::findOne($parentId)->model_id;
        
        if ($parentModelId <> 6) return $this->links[0]->parent_port + 1;
        else return $this->links[0]->parent_port + 25;
    }
	
	public function getParentPortName() : string {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentPortIndex = $this->links[0]->parent_port;
	    
	    return $parentDevice->model->port[$parentPortIndex];
	}
	
	public function getParentIp() : string {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    
	    if (!empty($parentDevice->ips)) return $parentDevice->ips[0]->ip;
	    else return 'Brak ip';
	}
	
	function isParent() : bool {
	    
	    return Tree::find()->where(['parent_device' => $this->id])->count() > 0 ? true : false;
	}
	
	function getParentConfigType() : int {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    
	    return $parentDevice->model->config;
	}
	
	function getSnmpDesc() : string {
	    
	    if ($this->parentConfigType == 1) return substr(snmpget($this->parentIp, 'wymyslj@k12spr0st3', '1.3.6.1.2.1.31.1.1.1.18.' . $this->parentPortIndex), 7);
	    elseif ($this->parentConfigType == 2) return substr(snmpget($this->parentIp, 'wymyslj@k12spr0st3', '1.0.8802.1.1.2.1.3.7.1.4.' . $this->parentPortIndex), 7);
	    else return 'Brak opisu';
	}
	
	function getSnmpVlan() : string {
	    
	    return substr(snmpget($this->parentIp, 'wymyslj@k12spr0st3', '1.3.6.1.2.1.17.7.1.4.5.1.1.' . $this->parentPortIndex), 9);
	}
}