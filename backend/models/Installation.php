<?php

namespace backend\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "{{%installation}}".
 *
 * @property integer $id
 * @property integer $address
 * @property integer $wire_length
 * @property string $wire_date
 * @property string $socket_date
 * @property string $wire_user
 * @property string $socket_user
 * @property integer $type
 * @property string $invoice_date
 * @property string $status
 */
class Installation extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_SOCKET = 'socket';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{installation}}';
    }

    /**
     * @inheritdoc
     */
	public function rules(){
		
		return [
			
            ['wire_date', 'date', 'format' => 'yyyy-MM-dd', 'message'=>'Zły format'],
            ['wire_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
//             ['wire_date', 'default', 'value' => new Expression('NOW()'), 'on' => self::SCENARIO_CREATE],
            ['wire_date', 'required', 'message'=>'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
            ['socket_date', 'date', 'format' => 'yyyy-MM-dd', 'message'=>'Zły format'],
            ['socket_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['socket_date', 'default', 'value' => NULL, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
			['socket_date', 'required', 'message'=>'Wartość wymagana', 'on' => self::SCENARIO_SOCKET],	
            
            ['invoice_date', 'date', 'format' => 'yyyy-MM-dd', 'message'=>'Zły format'],
            ['invoice_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['invoice_date', 'default', 'value' => NULL],
            
            ['wire_length', 'integer', 'message'=>'Wymagana liczba'],
            ['wire_length', 'required', 'message'=>'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
            
            //['wire_user', 'string', 'min' => 3, 'tooShort' => 'Minimum 3 znaki'],
			['wire_user', 'required', 'message'=>'Wartość wymagana', 'on' => self::SCENARIO_CREATE],
			//['wire_user', 'each', 'rule' => ['string']],
            
//             ['socket_user', 'string', 'min' => 3, 'tooShort'=>'Minimum 3 znaki'],
			['socket_user', 'required', 'on' => self::SCENARIO_SOCKET],
				
            ['type', 'integer'],
            ['type', 'required', 'message'=>'Wartość wymagana'],
            
            ['address', 'integer'],
            ['address', 'required', 'message'=>'Wartość wymagana'],
				
			['status', 'boolean'],
			['status', 'default', 'value' => true],
			['status', 'required', 'message'=>'Wartość wymagana'],
            
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			[
				['address', 'wire_length', 'type', 'status',
			  	'wire_date', 'socket_date',
				'wire_user', 'socket_user', 'invoice_date'], 		
				'safe'
			],
		];
	}
	
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['id', 'wire_date', 'wire_length', 'wire_user', 'type', 'address'];
		$scenarios[self::SCENARIO_UPDATE] = ['wire_date', 'wire_length', 'wire_user', 'socket_user', 'socket_date', 'invoice_date', 'status'];
		$scenarios[self::SCENARIO_SOCKET] = ['socket_date', 'socket_user'];
		 
		return $scenarios;
	}

    /**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'address' => 'Adres',
			'wire_length' => 'Długość',
			'wire_date' => 'Kabel',
			'socket_date' => 'Gniazdo',
			'wire_user' => 'Monter kabla',
			'socket_user' => 'Monter gniazda',
			'type' => 'Usługa',
			'invoice_date' => 'Zaksęgowano',
			'street' => 'Ulica',
			'house' => 'Blok',
			'house_detail' => 'Klatka',	
			'flat' => 'Lokal',
			'flat_detail' => 'Nazwa',
			'status' => 'Status'	
		);
	}
    
	/**
	 * @return \yii\db\ActiveQuery
	 */
    public function getModelAddress(){
    
    	//Installation jest robiona na jednym Address
    	return $this->hasOne(Address::className(), ['id'=>'address']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelType(){
    
    	//Installation jest robiona w jednym Type
    	return $this->hasOne(Type::className(), ['id'=>'type']);
    }
}
