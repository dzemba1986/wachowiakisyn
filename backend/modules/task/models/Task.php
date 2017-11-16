<?php

namespace backend\modules\task\models;

use Yii;
use common\models\User;
use yii\db\Expression;
use backend\models\Address;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $create
 * @property string $close
 * @property string $description
 * @property integer $category_id
 * @property integer $type_id
 * @property integer $add_user
 * @property integer $close_user
 * @property integer $status
 * @property integer $address_id
 */
class Task extends \yii\db\ActiveRecord
{	
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_CLOSE = 'close';
	
    public static function tableName(){
        
    	return '{{task}}';
    }
	
    public function attributes(){
    	
    	return [
    		'id',
    		'create',
    		'close',
    		'description',
    		'category_id',
    		'type_id',
    		'add_user',
    		'close_user',
    		'status',
    		'address_id'	
    	];
    }
    
    public function rules()
    {
        return [	
        		
            ['create', 'date', 'format' => 'yyyy-MM-dd H:i:s'],
        	['create', 'default', 'value' => date('Y-m-d H:i:s')],
        	['create', 'required', 'message'=>'Wartość wymagana'],	
            
        	['close', 'default', 'value' => new Expression('(now())::timestamp(0) without time zone')],
        	['close', 'required', 'message'=>'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
            
            ['status', 'boolean', 'trueValue' => true, 'falseValue' => false],
        	['status', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
        	['status', 'default', 'value' => null],	
            
            ['description', 'string'],
            
            ['address_id', 'integer'],
            ['address_id', 'required', 'message'=>'Wartość wymagana'],
        		
            ['category_id', 'integer'],
            ['category_id', 'required', 'message'=>'Wartość wymagana'],
            
            ['type_id', 'integer'],
            ['type_id', 'required', 'message'=>'Wartość wymagana'],
            
            ['add_user', 'integer'],
        	['add_user', 'default', 'value' => \Yii::$app->user->id],
            ['add_user', 'required', 'message'=>'Wartość wymagana'],
            
            ['close_user', 'integer'],
        	['close_user', 'default', 'value' => \Yii::$app->user->id],
        	['close_user', 'required', 'message'=>'Wartość wymagana'],

            [['id', 'create', 'close', 'address_id', 'category_id', 'type_id', 'description', 'add_user', 'close_user', 'status'], 'safe'],
        ];
    }
    
    public function scenarios()
    {
    	$scenarios = parent::scenarios();
    	$scenarios[self::SCENARIO_CREATE] = ['create', 'description', 'address_id', 'category_id', 'type_id', 'add_user'];
    	$scenarios[self::SCENARIO_UPDATE] = ['description', 'category_id', 'type_id'];
    	$scenarios[self::SCENARIO_CLOSE] = ['close', 'status', 'description', 'close_user'];
    	
    	return $scenarios;
    }
    
//     public function behaviors(){
    	
//     	return [
//     		'timestamp' => [
//     			'class' => 'yii\behaviors\TimestampBehavior',
//     			'attributes' => [
//     				ActiveRecord::EVENT_BEFORE_INSERT => ['create'],
//     			]
//     		]
//     	];
//     }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        	'create' => 'Utworzono',
        	'close' => 'Zamknięto',	
            'address_id' => 'Adres',
            'type_id' => 'Typ ',
            'category_id' => 'Kategoria',
            'description' => 'Opis',
            'add_user' => 'Dodał',
            'close_user' => 'Zamknął',
        ];
    }
    
    public function getAddress(){

        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }
    
    public function getType(){
    
    	return $this->hasOne(TaskType::className(), ['id' => 'type_id']);
    }
    
    public function getCategory(){
    
    	return $this->hasOne(TaskCategory::className(), ['id' => 'category_id']);
    }
    
    public function getAddUser(){
    
    	return $this->hasOne(User::className(), ['id' => 'add_user']);
    }
    
    public function getCloseUser(){
    
    	return $this->hasOne(User::className(), ['id' =>'close_user']);
    }
}
