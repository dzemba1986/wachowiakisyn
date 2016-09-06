<?php

namespace backend\models;

use Yii;
use common\models\User;
use yii\db\Expression;
use yii\web\JsExpression;

/**
 * This is the model class for table "task".
 *
 * @property integer $id
 * @property string $create
 * @property string $start_date
 * @property string $start_time
 * @property string $end_date
 * @property string $end_time
 * @property string $close_date
  
 * @property integer $all_day
 * @property string $class_name
 * @property string $color
 * @property string $description
 * @property string $installer
 * @property string $phone
 * @property double $cost
 * @property integer $editable
 
 * @property integer $category
 * @property integer $type
 * @property integer $add_user
 * @property integer $close_user
 * @property integer $status
 * @property integer $address
 */
class Task extends \yii\db\ActiveRecord
{	
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_CLOSE = 'close';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [	
        		
            ['create', 'date', 'format' => 'yyyy-MM-dd H:i:s'],
        	['create', 'default', 'value' => new Expression('NOW()')],
        	['create', 'required', 'message'=>'Wartość wymagana'],	
            
            ['start_date', 'date', 'format' => 'yyyy-MM-dd'],
            ['start_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['start_date', 'required', 'message'=>'Wartość wymagana'],
            
            ['start_time', 'date', 'format' => 'H:i:s', 'whenClient' => new JsExpression('function() {return false}')],
            ['start_time', 'match', 'pattern' => '/^(0[0-9]|1[0-9]|2[0123])\:([012345][0-9])\:([012345][0-9])$/u', 'message'=>'Zły format',
            	'whenClient' => new JsExpression('function() {return false}')
            ],
            ['start_time', 'required', 'message'=>'Wartość wymagana'],
            
            ['end_date', 'date', 'format' => 'yyyy-MM-dd'],
            ['end_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
            ['end_date', 'required', 'message'=>'Wartość wymagana'],
            
            ['end_time', 'date', 'format' => 'H:i:s', 'whenClient' => new JsExpression('function() {return false}')],
            ['end_time', 'match', 'pattern' => '/^(0[0-9]|1[0-9]|2[0123])\:([012345][0-9])\:([012345][0-9])$/u', 'message'=>'Zły format',
            	'whenClient' => new JsExpression('function() {return false}')
            ],
            ['end_time', 'required', 'message'=>'Wartość wymagana'],
        	
        	['close_date', 'default', 'value' => new Expression('(now())::timestamp(0) without time zone')],
//         	['close_date', 'date', 'format' => 'yyyy-MM-dd H:i:s', 'message' => 'Zły format'],
        	['close_date', 'required', 'message'=>'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
            
            ['color', 'string'],
        	['color', 'default', 'value' => null],
            
            ['class_name', 'string'],
        	['class_name', 'default', 'value' => null],
            
            ['all_day', 'boolean', 'trueValue' => true, 'falseValue' => false],
        	['all_day', 'default', 'value' => false],
        		
            ['editable', 'boolean', 'trueValue' => true, 'falseValue' => false],
        	['editable', 'default', 'value' => true],
        		
            ['status', 'boolean', 'trueValue' => true, 'falseValue' => false],
        	['status', 'default', 'value' => null],	
            
            ['cost', 'double', 'message' => 'Wartość liczbowa'],
        	['cost', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
            
            ['installer', 'string', 'message' => 'Wartość znakowa', 'whenClient' => new JsExpression('function() {return false}')],
        	['installer', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
            
            ['phone', 'trim'],
            ['phone', 'string', 'min'=>9, 'max'=>12, 'tooShort'=>'Minimum 9 znaków', 'tooLong'=>'Maksimum 12 znaków'],
            
            ['description', 'string'],
            
            ['address', 'integer'],
            ['address', 'required', 'message'=>'Wartość wymagana'],
            
            ['category', 'integer'],
            ['category', 'required', 'message'=>'Wartość wymagana'],
            
            ['type', 'integer'],
            ['type', 'required', 'message'=>'Wartość wymagana'],
            
            ['add_user', 'integer'],
            ['add_user', 'required', 'message'=>'Wartość wymagana'],
            
            ['close_user', 'integer'],
        	['close_user', 'required', 'message'=>'Wartość wymagana'],

            [
            	['id', 'create', 'start_date', 'start_time', 'end_date', 'end_time', 'all_day', 'address', 'category', 'type', 'description', 
                'add_user', 'close_user', 'installer', 'color', 'status', 'editable', 'class_name', 'cost', 'phone'], 
				'safe',
			],
        ];
    }
    
    public function scenarios()
    {
    	$scenarios = parent::scenarios();
    	$scenarios[self::SCENARIO_CREATE] = ['id', 'create', 'start_date', 'start_time', 'end_date', 'end_time', 'color', 'clas_name',
    		'all_day', 'editable', 'phone', 'description', 'address', 'category', 'type', 'add_user', 'phone'
    			];
    	$scenarios[self::SCENARIO_UPDATE] = ['start_date', 'start_time', 'end_date', 'end_time', 'color', 'class_name', 'all_day', 
    		'phone', 'description', 'category', 'type', 'phone', 'cost'
    			];
    	$scenarios[self::SCENARIO_CLOSE] = ['close_date', 'editable', 'status', 'cost', 'installer', 'description', 'close_user'];
    	
    	return $scenarios;
    }

        /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'start_date' => 'Start',
            'end_date' => 'Koniec',
            'url' => 'Url',
            'all_day' => 'Cały dzień',
            'color' => 'Kolor',
            'address' => 'Adres',
            'type' => 'Typ ',
            'category' => 'Kategoria',
            'description' => 'Opis',
            'add_user' => 'Dodał',
            'close_user' => 'Zamknął',
            'installer' => 'Wykonał',
            'phone' => 'Telefon',
            'cost' => 'Koszt',
            'street' => 'Ulica',
			'house' => 'Blok',
			'house_detail' => 'Klatka',	
			'flat' => 'Lokal',
			'flat_detail' => 'Nazwa',
        ];
    }
    public function getStart() {
        
        return $this->start_date.' '.$this->start_time;
    }
    
    public function getEnd() {
        
        return $this->end_date.' '.$this->end_time;
    }
    
    public function getModelAddress(){

        //Installation jest robiona na jednym Address
        return $this->hasOne(Address::className(), ['id'=>'address']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelTaskType(){
    
    	//Installation jest robiona w jednym Type
    	return $this->hasOne(TaskType::className(), ['id'=>'type']);
    }
    
    public function getModelTaskCategory(){
    
    	//Installation jest robiona w jednym Type
    	return $this->hasOne(TaskCategory::className(), ['id'=>'category']);
    }
    
    public function getModelAddUser(){
    
    	//Installation jest robiona w jednym Type
    	return $this->hasOne(User::className(), ['id'=>'add_user']);
    }
    
    public function getModelCloseUser(){
    
    	//Installation jest robiona w jednym Type
    	return $this->hasOne(User::className(), ['id'=>'close_user']);
    }
}
