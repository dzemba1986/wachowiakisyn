<?php

namespace backend\models;


/**
 * @property integer $id
 * @property integer $address_id
 * @property integer $wire_length
 * @property string $wire_date
 * @property string $socket_date
 * @property string $wire_user
 * @property string $socket_user
 * @property integer $type_id
 * @property string $invoice_date
 * @property string $status
 * @property array $connectionTypeIds
 */
class Installation extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_SOCKET = 'socket';
	
    public static function tableName()
    {
        return '{{installation}}';
    }
      
	public function rules(){
		
		return [
			
            ['wire_date', 'date', 'format' => 'yyyy-MM-dd', 'message' => 'Zły format'],
            ['wire_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message' => 'Zły format'],
            ['wire_date', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
            ['socket_date', 'date', 'format' => 'yyyy-MM-dd', 'message' => 'Zły format'],
            ['socket_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message' => 'Zły format'],
            ['socket_date', 'default', 'value' => NULL, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
			['socket_date', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_SOCKET],	
            
            ['invoice_date', 'date', 'format' => 'yyyy-MM-dd', 'message' => 'Zły format'],
            ['invoice_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message' => 'Zły format'],
            ['invoice_date', 'default', 'value' => NULL],
            
            ['wire_length', 'integer', 'message' => 'Wymagana liczba'],
            ['wire_length', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
			['wire_user', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
			['socket_user', 'required', 'on' => self::SCENARIO_SOCKET],
				
            ['type_id', 'integer'],
            ['type_id', 'required', 'message' => 'Wartość wymagana'],
            
            ['address_id', 'integer'],
            ['address_id', 'required', 'message' => 'Wartość wymagana'],
				
			['status', 'boolean'],
			['status', 'default', 'value' => true],
			['status', 'required', 'message' => 'Wartość wymagana'],
            
			[['address_id', 'wire_length', 'type_id', 'status', 'wire_date', 'socket_date', 'wire_user', 'socket_user', 'invoice_date'], 'safe'],
		];
	}
	
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['id', 'wire_date', 'wire_length', 'wire_user', 'type_id', 'address_id'];
		$scenarios[self::SCENARIO_UPDATE] = ['wire_date', 'wire_length', 'wire_user', 'socket_user', 'socket_date', 'invoice_date', 'status'];
		$scenarios[self::SCENARIO_SOCKET] = ['socket_date', 'socket_user'];
		 
		return $scenarios;
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'address_id' => 'Adres',
			'wire_length' => 'Długość',
			'wire_date' => 'Kabel',
			'socket_date' => 'Gniazdo',
			'wire_user' => 'Monter kabla',
			'socket_user' => 'Monter gniazda',
			'type_id' => 'Typ kabla',
			'invoice_date' => 'Zaksęgowano',
			'street' => 'Ulica',
			'house' => 'Blok',
			'house_detail' => 'Klatka',	
			'flat' => 'Lokal',
			'flat_detail' => 'Nazwa',
			'status' => 'Status'
		);
	}
    
    public function getAddress(){
    
    	return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }
    
    public function getType(){
    
    	return $this->hasOne(InstallationType::className(), ['id' => 'type_id']);
    }
    
    /**
     * @return array Powiązanie typów instalacji z typami umów
     */
    function getConnectionTypeIds() : array {
        
        if ($this->type_id == 1) return [1,3];
        elseif ($this->type_id == 2) return [2];
        elseif ($this->type_id == 3) return [];
        elseif ($this->type_id == 4) return [1,3];
    }
    
    public function afterSave($insert, $changedAttributes) {
        
        if ($insert) {
            $count = Installation::find()->where(['address_id' => $this->address_id, 'type_id' => $this->type_id])->andWhere(['is not', 'wire_date', null])->count();
            Connection::updateAll(['wire' => $count], ['type_id' => $this->connectionTypeIds, 'address_id' => $this->address_id]);
        }
        
        if (!$insert && array_key_exists('socket_date', $changedAttributes) && is_null($changedAttributes['socket_date'])) {
            $count = Installation::find()->where(['address_id' => $this->address_id, 'type_id' => $this->type_id])->andWhere(['is not', 'socket_date', null])->count();
            Connection::updateAll(['socket' => $count], ['type_id' => $this->connectionTypeIds, 'address_id' => $this->address_id]);
        }
    }
}