<?php

namespace common\models\soa;

use common\models\User;
use common\models\address\Address;
use common\models\crm\InstallTask;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\crm\Task;

/**
 * @property integer $id
 * @property integer $soa_id
 * @property string $ara_id
 * @property string $soa_id
 * @property string $create_at
 * @property string $start_at
 * @property string $conf_at
 * @property string $pay_at
 * @property string $close_at
 * @property date $synch_at
 * @property integer $add_by
 * @property integer $conf_by
 * @property integer $close_by
 * @property boolean $nocontract
 * @property boolean $vip
 * @property boolean $again
 * @property integer $wire
 * @property integer $socket
 * @property integer $address_id
 * @property integer $parent_port
 * @property integer $parent_device_id
 * @property integer $type_id
 * @property integer $package_id
 * @property string $phone
 * @property string $phone2
 * @property string $desc
 * @property string $desc_boa
 * @property integer $replaced_id
 * @property string phone_desc
 */

abstract class Connection extends ActiveRecord {
    
    const TYPE = 0;
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_CLOSE = 'close';
	
	const EVENT_CONF_CONN = 'conf-conn';
	const EVENT_PAY_CONN = 'pay-conn';
	const EVENT_CLOSE_CONN = 'close-conn';
    
	private $_name;
	private $_package;
	
    public static function tableName() {
        
        return '{{connection}}';
    }
    
    public static function instantiate($row) {
        
        if ($row['type_id'] == Internet::TYPE) return new Internet(); //1
        elseif ($row['type_id'] == Phone::TYPE) return new Phone(); //2
        elseif ($row['type_id'] == Tv::TYPE) return new Tv(); //3
    }

	public function rules() {
	    
		return [
		    ['start_at', 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => 'Zły format'],

		    ['conf_at', 'date', 'format' => 'yyyy-MM-dd', 'message' => 'Zły format'],
            ['conf_at', 'default', 'value' => null],

		    ['pay_at', 'date', 'format' => 'yyyy-MM-dd', 'message' => 'Zły format'],
            ['pay_at', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message' => 'Zły format'],
            ['pay_at', 'default', 'value' => null],

		    ['phone_at', 'date', 'format' => 'yyyy-MM-dd', 'message' => 'Zły format'],
			['phone_at', 'default', 'value' => null],
		    
            ['create_by', 'integer'],
			['create_by', 'required', 'message' => 'Wartość wymagana'],	
		    
            ['address_id', 'integer'],
			['address_id', 'required', 'message' => 'Wartość wymagana'],
		    
			['device_id', 'integer'],
		    
            ['port', 'integer'],
		    
		    ['ara_id', 'integer'],
			['ara_id', 'required', 'message' => 'Wartość wymagana'],
				
			['soa_id', 'integer'],
			['soa_id', 'required', 'message' => 'Wartość wymagana'],

		    ['soa_number', 'string'],
			['soa_number', 'required', 'message' => 'Wartość wymagana'],
            
            ['phone', 'trim'],
            ['phone', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Min. {min} znaków', 'tooLong' => 'Max. {max} znaków'],
            
            ['phone2', 'trim'],
            ['phone2', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Min. {min} znaków', 'tooLong' => 'Max. {max} znaków'],
		];
	}
	
	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['create_at', 'create_by', 'start_at'];
		$scenarios[self::SCENARIO_UPDATE] = ['pay_at'];
		$scenarios[self::SCENARIO_CLOSE] = ['close_at', 'close_by'];
		 
		return $scenarios;
	}
	
	public function behaviors() {
	    
	    return [
	        [
	            'class' => AttributeBehavior::class,
	            'attributes' => [
	                self::EVENT_BEFORE_INSERT => 'type_id',
	            ],
	            'value' => static::TYPE,
	        ],
	        [
	            'class' => TimestampBehavior::class,
	            'attributes' => [
	                self::EVENT_BEFORE_INSERT => ['create_at'],
	                self::EVENT_CONF_CONN => ['conf_at'],
	                self::EVENT_PAY_CONN => ['pay_at'],
	                self::EVENT_CLOSE_CONN => ['close_at'],
	            ],
	            'value' => new Expression('NOW()'),
	        ],
// 	        [
// 	            'class' => BlameableBehavior::class,
// 	            'attributes' => [
// 	                self::EVENT_CONF_CONN => ['conf_by'],
// 	                self::EVENT_PAY_CONN => ['pay_by'],
// 	                self::EVENT_CLOSE_CONN => ['close_by'],
// 	            ],
// 	            'value' => \Yii::$app->user->id,
// 	        ],
        ];
	}

	public function attributeLabels() {
	    
		return [
			'ara_id' => 'ARA ID',
			'soa_id' => 'SOA ID',
			'soa_number' => 'Numer SOA',
		    'create_at' => 'Utworzone',
			'start_at' => 'Umowa od',
		    'exec_from' => 'Wykonać od',
		    'exec_to' => 'Wykonać do',
			'pay_at' => 'Płatność',
			'synch_at' => 'Synchronizacja',
			'close_at' => 'Rezygnacja',
			'create_by' => 'Dodał',
			'conf_by' => 'Skonfigurował',
			'address_id' => 'Adres',
			'device_id' => 'Urządzenie',
			'port' => 'Port',
			'mac' => 'Mac',
			'type_id' => 'Usługa',
			'package_id' => 'Pakiet',		
			'phone' => 'Tel. domowy',
			'phone2' => 'Tel. komórkowy',
			'info' => 'Info',
			'info_boa' => 'Info Boa',
			'again'	=> 'Ponowne',
			'task_id' => 'Zadania',
			'nocontract' => 'Bez umowy',
			'vip' => 'Vip',
			'socket' => 'Gniazdo',
			'wire' => 'Kabel',	
			'street' => 'Ulica',
			'house' => 'Blok',
			'house_detail' => 'Klatka',	
			'flat' => 'Lokal',
			'flat_detail' => 'Nazwa',
		    'phone_desc' => 'Mac/port'
		];
	}
	
    public function getAddress() {
    
        return $this->hasOne(Address::class, ['id' => 'address_id'])->select('id, t_ulica, ulica_prefix, ulica, dom, dom_szczegol, lokal, lokal_szczegol');
    }
    
    public function getTasks() {
        
        return $this->hasMany(Task::class, ['id' => 'task_id'])->viaTable('connection_task', ['connection_id' => 'id']);
    }
    
    public function getCloseBy() {
        
        return $this->hasOne(User::class, ['id' => 'close_by']);
    }
    
    public function getType() {
        
        return $this->hasOne(Package::class, ['id' => 'type_id']);
    }
    
    public function getPackage() {
        
        return $this->hasOne(Package::class, ['id' => 'package_id']);
    }
    
    public function isActive() : bool {
        
        return !$this->close_date ? true : false;
    }
}