<?php

namespace backend\models;

use backend\modules\task\models\InstallTask;
use common\models\User;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $ara_id
 * @property string $soa_id
 * @property string $start_date
 * @property string $conf_date
 * @property string $pay_date
 * @property string $close_date
 * @property string $phone_date
 * @property date $synch_date
 * @property integer $add_user
 * @property integer $conf_user
 * @property integer $close_user
 * @property boolean $nocontract
 * @property boolean $vip
 * @property boolean $again
 * @property integer $wire
 * @property integer $socket
 * @property integer $address_id
 * @property integer $port
 * @property integer $device_id
 * @property integer $host_id
 * @property string $mac
 * @property integer $type_id
 * @property integer $package_id
 * @property string $phone
 * @property string $phone2
 * @property string $info
 * @property string $info_boa
 * @property integer $task_id
 * @property integer $soa_id
 * @property integer $replaced_id
 * @property string $exec_date
 * @property array $installations
 * @property backend\models\Address $address
 * @property backend\models\Package $package
 * @property backend\models\ConnectionType $type
 * @property common\models\User $addUser
 * @property common\models\User $configureUser
 * @property backend\modules\task\models\InstallTask $task
 * @property backend\models\Host $host
 */
class Connection extends ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_CLOSE = 'close';
	const SCENARIO_CREATE_INSTALLATION = 'create_installation';
	const SCENARIO_TASK = 'task';

    public static function tableName()
    {
        return '{{connection}}';
    }

	public function rules()
	{
		return [
            
            ['ara_id', 'string', 'min' => 5, 'max' => 6, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
				
			['soa_id', 'integer'],
			['soa_id', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
            ['phone', 'trim'],
            ['phone', 'string', 'min'=>9, 'max'=>13, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
            
            ['phone2', 'trim'],
            ['phone2', 'string', 'min'=>9, 'max'=>13, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
            
			['mac', 'string', 'min'=>12, 'max'=>17, 'tooShort'=>'Za mało znaków', 'tooLong'=>'Za dużo znaków'],
			['mac', 'default', 'value'=>NULL],
			['mac', MacaddressValidator::className(), 'message'=>'Zły format'],
			['mac', 'trim', 'skipOnEmpty' => true],
				
            ['port', 'integer'],
			['port', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE_INSTALLATION],
			
			['device_id', 'integer'],
			['device_id', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE_INSTALLATION],
				
		    ['start_date', 'date', 'format'=>'php:Y-m-d H:i:s', 'message'=>'Zły format'],
			['start_date', 'default', 'value' => new \yii\db\Expression('NOW()')],
                      
		    ['conf_date', 'date', 'format'=>'yyyy-MM-dd', 'message'=>'Zły format'],
            ['conf_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['conf_date', 'default', 'value'=>NULL],
            
		    ['pay_date', 'date', 'format'=>'yyyy-MM-dd', 'message'=>'Zły format'],
            ['pay_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['pay_date', 'default', 'value'=>NULL],
            
		    ['phone_date', 'date', 'format'=>'yyyy-MM-dd', 'message'=>'Zły format'],
			['phone_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
			['phone_date', 'default', 'value'=>NULL],
            
		    ['close_date', 'date', 'format'=>'php:Y-m-d H:i:s', 'message'=>'Zły format'],
            //['close_date', 'match', 'pattern'=>'/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['close_date', 'default', 'value'=>NULL],
            
            ['add_user', 'integer'],
			['add_user', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],	
            
            ['address_id', 'integer'],
			['address_id', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
            ['close_user', 'integer'],
			['close_user', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],
            
            ['task_id', 'integer'],

			[
				['ara_id', 'soa_id', 'start_date', 'conf_date', 'pay_date', 'close_date', 'phone_date',
				'add_user', 'conf_user', 'close_user', 'vip', 'nocontract', 'again',
				'address_id', 'phone', 'phone2', 'info', 'info_boa',
				'port', 'device_id', 'mac', 'type_id', 'package_id', 'task_id'],
				'safe'
			],	
		];
	}
	
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['id', 'soa_id', 'ara_id', 'start_date', 'add_user', 'nocontract', 'vip', 'port', 'mac',
				'phone', 'phone2', 'info_boa', 'device_id', 'package_id', 'address_id', 'type_id', 'phone_date'
		];
		$scenarios[self::SCENARIO_UPDATE] = ['conf_date', 'pay_date', 'phone_date', 'close_date', 'nocontract', 'vip', 'port',
				'mac', 'phone', 'phone2', 'info', 'info_boa', 'device_id', 'wire', 'socket'
		];
		$scenarios[self::SCENARIO_CLOSE] = ['close_date', 'close_user'];
		$scenarios[self::SCENARIO_CREATE_INSTALLATION] = ['port', 'device_id', 'info'];
		$scenarios[self::SCENARIO_TASK] = ['task_id', 'mac'];
		 
		return $scenarios;
	}

	public function attributeLabels()
	{
		return [
			'id' => 'Id',
			'ara_id' => 'Ara',
			'soa_id' => 'Soa',
			'start_date' => 'Wpis od',
			'conf_date' => 'Konfiguracja',
			'soa_date' => 'Umowa od',
			'pay_date' => 'Płatność',
			'close_date' => 'Rezygnacja',
			'phone_date' => 'Przeniesienie',
			'install_date' => 'Data instalacji',
			'synch_date' => 'Synchronizacja',
		    'exec_date' => 'Skonfigurować',
			'add_user' => 'Dodał',
			'conf_user' => 'Skonfigurował',
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
		];
	}
	
	/**
	 * @return array Powiązanie typów umów z typami instalacji
	 */
	function getInstallationTypeIds() : array {
	    
	    if ($this->type_id == 1) return [1,4];
	    elseif ($this->type_id == 2) return [2];
	    elseif ($this->type_id == 3) return [1,4];
	}
	
    public function getInstallations($type = false) {
    
        if (!$type)
            return $this->hasMany(Installation::className(), ['address_id' => 'address_id'])->where(['status' => true]);
        else {
            return $this->hasMany(Installation::className(), ['address_id' => 'address_id'])->where(['status' => true, 'type_id' => $this->getInstallationTypeIds()]);
        }
    }
    
    public function getAddress(){
    
    	return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }
    
    public function getTask(){
    
    	return $this->hasOne(InstallTask::className(), ['id' => 'task_id']);
    }
    
    public function getType(){
    
    	return $this->hasOne(ConnectionType::className(), ['id' => 'type_id']);
    }
    
    public function getPackage(){
    
    	return $this->hasOne(Package::className(), ['id' => 'package_id']);
    }
    
    public function getConfUser() {
        
        return $this->hasOne(User::className(), ['id' => 'conf_user']);
    }
    
    public function getCloseUser() {
        
        return $this->hasOne(User::className(), ['id' => 'close_user']);
    }
    
    public function getHost() {
        
        return $this->hasOne(Host::className(), ['id' => 'host_id']);
    }
    
    function isActive() : bool {
        
        return is_null($this->close_date) ? true : false;
    }
    
    function canConfigure() : bool {
        //kabel musi być gdyż po konfiguracji szczurek nie ma już na liście odpowiedniego portu do wyboru 
        return !$this->nocontract && is_null($this->close_date) && is_null($this->host_id) && $this->type_id <> 2 && $this->wire > 0 && $this->exec_date <= date('Y-m-d')? true : false;
    }
    
    function beforeSave($insert) {
        
        if (!$insert) {
            if (is_null($this->close_date) && !is_null($this->oldAttributes['close_date'])) {
                $this->close_user = null;
                $this->conf_date = null;
                
                $history = \Yii::createObject([
                    'class' => History::className(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'desc' => 'Cofnięcie rezygnacji',
                    'address_id' => $this->address_id,
                    'connection_id' => $this->id,
                    'created_by' => \Yii::$app->user->id
                ]);
                
                if (!$history->save()) throw new Exception('Błąd zapisu historii');
            }
            
            if (array_key_exists('conf_date', $this->dirtyAttributes) && $this->conf_date && !$this->oldAttributes['conf_date']) {
                
                $history = \Yii::createObject([
                    'class' => History::className(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'desc' => 'Konfiguracja',
                    'address_id' => $this->address_id,
                    'connection_id' => $this->id,
                    'created_by' => \Yii::$app->user->id
                ]);
                
                if (!$history->save()) throw new Exception('Błąd zapisu historii');
            }
        }
        
        if (!parent::beforeSave($insert)){
            return false;
        }
        
        return true;
    }
    
    function afterSave($insert, $changedAttributes) {
        
        if (!$insert) {
            if (array_key_exists('close_date', $changedAttributes) && is_null($changedAttributes['close_date'])) {
                
                $history = \Yii::createObject([
                    'class' => History::className(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'desc' => 'Rezygnacja z umowy',
                    'address_id' => $this->address_id,
                    'connection_id' => $this->id,
                    'created_by' => \Yii::$app->user->id
                ]);
                
                if (!$history->save()) throw new Exception('Błąd zapisu historii');
                
                if (($this->type_id != 2 && array_key_exists('host_id', $changedAttributes)) && !is_null($changedAttributes['host_id'])) {
                    if (self::find()->where(['host_id' => $changedAttributes['host_id']])->count() == 0) {
                        $host = Host::findOne($changedAttributes['host_id']);
                        $host->status = false;
                        $host->dhcp = false;
                        $host->smtp = false;
                        $host->mac = null;
                        
                        if (!$host->save()) throw new Exception('Błąd zapisu hosta');
                        
                        foreach ($host->ips as $ip)
                            if (!$ip->delete()) throw new Exception('Błąd usuwania IP');
    
                    }
                }
            } 
        }
    }
}