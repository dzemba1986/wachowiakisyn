<?php

namespace backend\models;

use Yii;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;

/**
 * This is the model class for table "{{%connection}}".
 *
 * @property integer $id
 * @property string $ara_id
 * @property string $soa_id
 * @property string $start_date
 * @property string $conf_date
 * @property string $pay_date
 * @property string $close_date
 * @property string $phone_date
 * @property integer $add_user
 * @property integer $conf_user
 * @property integer $close_user
 * @property integer $nocontract
 * @property integer $vip
 * @property integer $again
 * @property integer $wire
 * @property integer $socket
 * @property integer $address
 * @property integer $port
 * @property integer $device
 * @property integer $host
 * @property string $mac
 * @property integer $type
 * @property integer $package
 * @property string $phone
 * @property string $phone2
 * @property string $info
 * @property string $info_boa
 * @property integer $task
 * @property Installation $modelInstallationByType
 * @property Installation $modelInstallations
 * @property Address $modelAddress
 * @property Package $modelPackage
 * @property Type $modelType
 * @property User $addUser
 * @property User $configureUser
 */
class Connection extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_CLOSE = 'close';
	const SCENARIO_CREATE_INSTALLATION = 'create_installation';
	const SCENARIO_TASK = 'task';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'connection';
    }

    /**
     * @inheritdoc
     */
	public function rules()
	{
		return [
            
            ['ara_id', 'string', 'min' => 5, 'max' => 6, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
				
			['soa_id', 'integer'],
			['soa_id', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
            ['phone', 'trim'],
            ['phone', 'string', 'min'=>9, 'max'=>12, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
            
            ['phone2', 'trim'],
            ['phone2', 'string', 'min'=>9, 'max'=>12, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
            
			['mac', 'string', 'min'=>12, 'max'=>17, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
			['mac', 'default', 'value'=>NULL],
			['mac', MacaddressValidator::className(), 'message'=>'Zły format'],
// 			['mac', 'unique', 'targetClass' => 'backend\models\Connection', 'message' => 'Mac zajęty',
// 				'when' => function ($model) { return !is_null($model->conf_date); } 
// 			],
			['mac', 'trim', 'skipOnEmpty' => true],
				
            ['port', 'integer'],
			['port', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE_INSTALLATION],
			
			['device', 'integer'],
			['device', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE_INSTALLATION],
				
			['start_date', 'date', 'format'=>'yyyy-MM-dd'],
			['start_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
			['start_date', 'default', 'value' => new \yii\db\Expression('NOW()')],
                      
            ['conf_date', 'date', 'format'=>'yyyy-MM-dd'],
            ['conf_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['conf_date', 'default', 'value'=>NULL],
            
            ['pay_date', 'date', 'format'=>'yyyy-MM-dd'],
            ['pay_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['pay_date', 'default', 'value'=>NULL],
            
			['phone_date', 'date', 'format'=>'yyyy-MM-dd'],
			['phone_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
			['phone_date', 'default', 'value'=>NULL],
            
            ['close_date', 'date', 'format'=>'yyyy-MM-dd'],
            ['close_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['close_date', 'default', 'value'=>NULL],
            
            ['add_user', 'integer'],
			['add_user', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],	
            
            ['address', 'integer'],
			['address', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
            ['close_user', 'integer'],
			['close_user', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],
            
            ['task', 'integer'],

			[
				['ara_id', 'soa_id', 'start_date', 'conf_date', 'pay_date', 'close_date', 'phone_date',
				'add_user', 'conf_user', 'close_user', 'vip', 'nocontract', 'again',
				'address', 'phone', 'phone2', 'info', 'info_boa',
				'port', 'device', 'mac', 'type', 'package', 'task'],
				'safe'
			],	
		];
	}
	
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['id', 'soa_id', 'ara_id', 'start_date', 'add_user', 'nocontract', 'vip', 'port', 'mac',
				'phone', 'phone2', 'info_boa', 'device', 'package', 'address', 'type', 'phone_date'
		];
		$scenarios[self::SCENARIO_UPDATE] = ['conf_date', 'pay_date', 'phone_date', 'close_date', 'nocontract', 'vip', 'port',
				'mac', 'phone', 'phone2', 'info', 'info_boa', 'device'
		];
		$scenarios[self::SCENARIO_CLOSE] = ['close_date', 'close_user'];
		$scenarios[self::SCENARIO_CREATE_INSTALLATION] = ['port', 'device', 'info'];
		$scenarios[self::SCENARIO_TASK] = ['task', 'mac'];
		 
		return $scenarios;
	}

    /**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'ara_id' => 'Ara',
			'start_date' => 'Data od',
			'conf_date' => 'Konfiguracja',
			'pay_date' => 'Płatność',
			'close_date' => 'Rezygnacja',
			'phone_date' => 'Przeniesienie',
			'install_date' => 'Data instalacji',
			'add_user' => 'Dodał',
			'conf_user' => 'Skonfigurował',
			'install_user' => 'Zainstalował',
			'address' => 'Adres ID',
			'device' => 'Urządzenie',
			'switch_string' => 'Switch',
			'port' => 'Port',
			'mac' => 'Mac',
			'type' => 'Usługa',
			'package' => 'Pakiet',		
			'speed' => 'Prędkość',
			'phone' => 'Tel. domowy',
			'phone2' => 'Tel. komórkowy',
			'info' => 'Info',
			'info_boa' => 'Info Boa',
			'again'	=> 'Ponowne',
			'task' => 'Zadania',
			'nocontract' => 'Bez umowy',
			'vip' => 'Vip',
			'street' => 'Ulica',
			'house' => 'Blok',
			'house_detail' => 'Klatka',	
			'flat' => 'Lokal',
			'flat_detail' => 'Nazwa',
			'typeName' => 'Usługa',
			'fullAddress' => 'Adres',					
		];
	}
    
    //relacje
  
	/**
	 * @return \yii\db\ActiveQuery
	 */
	//Instalacje na tym samym adresie tego samego typu co umowa
    public function getModelInstallationsByType(){
        
        return $this->getModelInstallations()->where([Installation::tableName().'.type' => $this->getInstallationType()])->all();
        
    	//return Installation::find()->where(['installation.type' => $ins_type])->andWhere(['installation.address' => $this->address])->all();
    }
    
    //Instalacje na tym samym adresie różnego typu niż umowa
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelInstallations(){
    
    	//Connection ma wiele instalacji na danym adresie
    	return $this->hasMany(Installation::className(), ['address'=>'address']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelAddress(){
    
    	//Connection ma tylko 1 Address
    	return $this->hasOne(Address::className(), ['id'=>'address']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelTask(){
    
    	//Connection ma zgłoszone zdarzenie
    	return $this->hasOne(Task::className(), ['id'=>'task']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelType(){
    
    	return $this->hasOne(Type::className(), ['id'=>'type']);
    }
    
    public function getInstallationType() {
    	
    	if ($this->type == 1){ //jeżeli internet
    		return 1; //ethernet
    	}
    	elseif ($this->type == 2){ //jeżeli telefon
    		return 2; //kabel telefoniczny
    	}
    	elseif ($this->type == 3){ //jeżeli telewizja
    		if ($this->package == 5) 
    			return 1; //ethernet

    	//rozbudować w miarę potrzeby
    	}
    }
}
