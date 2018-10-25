<?php

namespace common\models\seu\devices;

use backend\modules\address\models\Address;
use common\models\seu\devices\traits\Ip;
use common\models\seu\devices\traits\ParentDevice;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property boolean $status
 * @property string $name
 * @property string $proper_name
 * @property string $desc
 * @property integer $address_id
 * @property integer $type_id
 * @property string $mixName 
 * @property backend\modules\address\models\Address $address
 * @property common\models\seu\devices\DeviceType $type
 * @property common\models\seu\Link[] $links
 */

abstract class Device extends ActiveRecord {
    
    const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_REPLACE = 'replace';
	
	private $addressName = NULL;
	private $prefix = NULL;
	private $mixName = NULL;
	private $typeName = NULL;
	private $canHasIp = NULL;
	private $canBeParent = NULL;
	
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
	        'address_id',
	        'type_id',
	    ];
	}
	
	public function fields() {
	    
	    return [
	        'id',
	        'status',
	        'name',
	        'proper_name',
	        'desc',
	        'address_id',
	        'type_id',
	    ];
	}
	
	public static function instantiate($row) {
	    
		switch ($row['type_id']) {
			case Host::TYPE:
				return Host::instantiate($row);
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
			case OpticalTransmitter::TYPE:
			    return new OpticalTransmitter();
			case OpticalAmplifier::TYPE:
			    return new OpticalAmplifier();
			case OpticalSplitter::TYPE:
			    return new OpticalSplitter();
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
		    
            ['address_id', 'integer'],
            ['address_id', 'required'],
            
            ['type_id', 'integer'],
            ['type_id', 'required', 'message' => 'Wartość wymagana'],           
		];
	}
    
	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['status', 'name', 'proper_name', 'address_id', 'desc', 'type_id'];
		$scenarios[self::SCENARIO_UPDATE] = ['status', 'name', 'proper_name', 'address_id', 'desc'];
		$scenarios[self::SCENARIO_REPLACE] = ['status', 'name', 'proper_name'];
			
		return $scenarios;
	}
	
	public function attributeLabels() {
	    
		return [
			'id' => 'ID',
			'status' => 'Status',
            'name' => 'Nazwa',
			'proper_name' => 'Nazwa własna',	
            'desc' => 'Opis',
			'address_id' => 'Adres',
			'type_id' => 'Typ',
		];
	}
	
	public final function getAddress() {
	    
	    return $this->hasOne(Address::className(), ['id' => 'address_id'])->select('id, t_ulica, ulica_prefix, ulica, dom, dom_szczegol, lokal, lokal_szczegol, pietro');
	}
	
	public final function getType() {
	    
	    return $this->hasOne(DeviceType::className(), ['id' => 'type_id']);
	}
	
	protected function getPrefix() {
	    
	    if (is_null($this->prefix)) $this->prefix = $this->getType()->select('prefix')->asArray()->one()['prefix'];
	    
	    return $this->prefix;
	}
	
	public function getMixName($pl = true) : string {
	    
	    if (is_null($this->mixName)) {
    	    if ($pl)
    	        $this->mixName = $this->proper_name ? $this->getPrefix() . $this->name . '_' . $this->proper_name : $this->getPrefix() . $this->name;
            else {
                $trans = ['Ą' => 'A', 'Ł' => 'L', 'Ę' => 'E', 'Ó' => 'O', 'Ś' => 'S', 'Ć' => 'C', 'Ż' => 'Z', 'Ź' => 'Z', 'Ń' => 'N', 'ą' => 'a', 'ł' => 'l', 'ę' => 'e', 'ó' => 'o', 'ś' => 's', 'ć' => 'c', 'ż' => 'z', 'ź' => 'z', 'ń' => 'n'];
    	        $this->mixName = $this->proper_name ? $this->getPrefix() . strtr($this->name,  $trans) . '_' . strtr($this->proper_name, $trans) : $this->getPrefix() . strtr($this->name, $trans);
            }
	    }
	    
	    return $this->mixName;
    }
    
    public function getAddressName() : string {
        
        if (is_null($this->addressName)) $this->addressName = $this->getAddress()->one()->toString();
            
        return $this->addressName;
    }
    
    public function getTypeName() : string {
        
        if (is_null($this->typeName)) $this->typeName = $this->getType()->select('name')->asArray()->one()['name'];
        
        return $this->typeName;
    }
    
    public static function getController($typeId, $technic = NULL) {
        
        switch ($typeId) {
            case Host::TYPE:
                if ($technic == HostEthernet::TECHNIC) return 'host-ethernet';
                elseif ($technic == HostRfog::TECHNIC) return 'host-rfog';
            case Swith::TYPE:
                return 'swith';
            case Router::TYPE:
                return 'router';
            case Camera::TYPE:
                return 'camera';
            case GatewayVoip::TYPE:
                return 'gateway-voip';
            case MediaConverter::TYPE:
                return 'media-converter';
            case Server::TYPE:
                return 'server';
            case Virtual::TYPE:
                return 'virtual';
            case Root::TYPE:
                return 'root';
            case Ups::TYPE:
                return 'ups';
            case Radio::TYPE:
                return 'radio';
            case OpticalTransmitter::TYPE:
                return 'optical-transmitter';
            case OpticalAmplifier::TYPE:
                return 'optical-amplifier';
            case OpticalSplitter::TYPE:
                return 'optical-splitter';
        }
    }
    
    public function getCanHasIp() {

        if (is_null($this->canHasIp)) $this->canHasIp = in_array(Ip::class, class_uses(static::className()));
        
        return $this->canHasIp;
    }
    
    public function getCanBeParent() {
        
        if (is_null($this->canBeParent)) $this->canBeParent = in_array(ParentDevice::class, class_uses(static::className()));
        
        return $this->canBeParent;
    }
    
    public static function getCanSwitchToParentTypeIds() {
        
        return [Swith::TYPE];
    }
    
    public function addOnTree() {
        
        $this->status = true;
    }
    
    public function deleteFromTree() {
        
        $this->address_id = 1;
        $this->status = null;
        $this->name = null;
        $this->proper_name = null;
    }
}