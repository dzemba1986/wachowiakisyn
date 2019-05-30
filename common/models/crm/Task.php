<?php

namespace common\models\crm;

use common\models\User;
use common\models\address\Address;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\JsExpression;
use common\models\soa\Connection;

/**
 * @property integer $id
 * @property string $create_at
 * @property string $start_at
 * @property string $end_at
 * @property string $close_at
 * @property string $close_at
 * @property string $desc
 * @property string $close_desc
 * @property integer $category_id
 * @property integer $type_id
 * @property integer $create_by
 * @property integer $close_by
 * @property integer $receive_by
 * @property integer $status
 * @property integer $label_id
 * @property integer $address_id
 * @property boolean $fulfit
 * @property boolean $programme
 */

abstract class Task extends ActiveRecord {
    
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_CLOSE = 'close';
	
	const EVENT_CLOSE_TASK = 'close-task';
	
	const TYPE = null;
	const STATUS = [
	    0 => 'Otwarte',
	    1 => 'Zamknięte',
	    2 => 'Przyjęte',
	];
	const RECEIVE_BY = [
	    1 => 'Serwis',
	    2 => 'Szczurek',
	];
	const PAY_BY = [
	    1 => 'Klient',
	    2 => 'WTvK',
	];
	
	public $day;
	public $start_time;
	public $end_time;
	public $comments_count;
	public $address_string;
	
	public static function instantiate($row) {

	    if ($row['type_id'] == FailureTask::TYPE) return new FailureTask(); //6
	    elseif ($row['type_id'] == ConnectTask::TYPE) return new ConnectTask(); //3
	    elseif ($row['type_id'] == DisconnectTask::TYPE) return new DisconnectTask(); //7
	    elseif ($row['type_id'] == InstallTask::TYPE) return new InstallTask(); //4
	    elseif ($row['type_id'] == ConfigTask::TYPE) return new ConfigTask(); //5
	    elseif ($row['type_id'] == DeviceTask::TYPE) return new DeviceTask(); //2
	    elseif ($row['type_id'] == SelfTask::TYPE) return new SelfTask(); //8
	    elseif ($row['type_id'] == Blockage::TYPE) return new Blockage(); //9
	}
	
    public static function tableName() {
        
    	return '{{task}}';
    }
	
    public static function columns() {
    	
    	return [
    		'id',
    		'create_at',
    		'close_at',
    	    'start_at',
			'end_at',
    	    'exec_from',
			'exec_to',
    		'create_by',
    	    'receive_by', // 1 -> serwis; 2 -> szczurek
    		'close_by',
    		'address_id',	
    		'desc',
    	    'close_desc',
    		'status', // 0 -> otwarte; 1 -> zamknięte; 3 -> przyjęte
    		'type_id',
    		'category_id', // w zależności od typu
    		'subcategory_id', // w zależności od typu i kategorii
    	    'fulfit', // czy wykonano zadanie
    	    'programme', //czy umówiono
    	];
    }
    
    public function attributes() {
        
        return static::columns();
    }
    
    public function rules() {
        
        return [
            ['start_at', 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => 'Zły format'],
            
            ['end_at', 'date', 'format' => 'php:Y-m-d H:i:s', 'message' => 'Zły format'],
            
            ['day', 'date', 'format' => 'yyyy-MM-dd', 'message' => 'Zły format'],
            ['day', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message' => 'Zły format'],
            
            ['start_time', 'date', 'format' => 'php:H:i', 'message' => 'Zły format'],
            ['start_time', 'match', 'pattern' => '/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/u', 'message' => 'Zły format'],
            
            ['end_time', 'date', 'format' => 'php:H:i', 'whenClient' => new JsExpression('function() {return false}')],
            ['end_time', 'match', 'pattern' => '/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/u', 'message' => 'Zły format'],
            ['end_time', 'compare', 'compareAttribute' => 'start_time', 'operator' => '>', 'message' => 'Wartość > od czasu "od"'],
            
            ['receive_by', 'required', 'message' => 'Wartość wymagana'],
            ['receive_by', 'integer'],
            ['receive_by', 'in', 'range' => [1, 2]],
            
            ['address_id', 'integer'],
            
            ['desc', 'string', 'max' => 1000, 'tooLong' => 'Maximum {max} znaków'],
            
            ['close_desc', 'string', 'max' => 1000, 'tooLong' => 'Maximum {max} znaków'],

            ['category_id', 'integer'],
            
            ['label_id', 'integer'],
            ['label_id', 'default', 'value' => null],
            
            ['receive_by', 'required', 'message' => 'Wartość wymagana'],
            
            ['fulfit', 'boolean'],

            ['programme', 'boolean'],
        ];
    }
    
    public function scenarios() {
        
    	$scenarios = parent::scenarios();
    	$scenarios[self::SCENARIO_CREATE] = ['create_at', 'start_at', 'end_at', 'exec_from', 'exec_to', 'create_by', 'receive_by', 'address_id',
    	    'desc', 'category_id', 'type_id', 'day', 'start_time', 'end_time'
    	];
    	$scenarios[self::SCENARIO_UPDATE] = ['start_at', 'end_at', 'exec_from', 'exec_to', 'receive_by', 'day', 'start_time', 'end_time', 'desc', 'category_id', 
    	    'label_id', 'receive_by'
    	];
    	$scenarios[self::SCENARIO_CLOSE] = ['close_at', 'close_by', 'close_desc', 'status', 'fulfit'];
    	
    	return $scenarios;
    }
    
    public function attributeLabels() {
        
        return [
            'id' => 'ID',
        	'create_at' => 'Dodano',
        	'close_at' => 'Zamknięto',
            'start_at' => 'Start',
            'end_at' => 'Koniec',
            'address_id' => 'Adres',
            'type_id' => 'Typ ',
            'category_id' => 'Kategoria',
            'subcategory_id' => 'Podkat.',
            'desc' => 'Opis',
            'close_desc' => 'Wykonano',
            'create_by' => 'Autor',
            'close_by' => 'Zamknął',
            'receive_by' => 'Kalendarz',
            'label_id' => 'Etykieta',
            'fulfit' => 'Wykonane',
            'programme' => 'Kalendarz',
        ];
    }
    
    public function behaviors() {
        
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => 0,
            ],
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
                    self::EVENT_CLOSE_TASK => ['close_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['create_by'],
                    self::EVENT_CLOSE_TASK => ['close_by'],
                ],
                'value' => \Yii::$app->user->id,
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_UPDATE => 'status',
                ],
                'value' => function () {
                    return ($this->status != 2 && !$this->close_at && !$this->close_by) ? 2 : 1;
                }
            ],
        ];
    }
    
    public function beforeValidate() {
        
        if ($this->day && $this->start_time) $this->start_at = $this->day . ' ' . $this->start_time . ':00';
        if ($this->day && $this->start_time) $this->end_at = $this->day . ' ' . $this->end_time . ':00';
        if ($this->category_id) $this->category_id = (int) $this->category_id;
        if ($this->receive_by) $this->receive_by = (int) $this->receive_by;

        return parent::beforeValidate();
    }
    
    public function afterFind() {
        
        if ($this->start_at && $this->end_at) {
            $this->day = date('Y-m-d', strtotime($this->start_at));
            $this->start_time = date('H:i', strtotime($this->start_at));
            $this->end_time = date('H:i', strtotime($this->end_at));
        }
        
        parent::afterFind();
    }
    
    public function getAddress() {

        return $this->hasOne(Address::class, ['id' => 'address_id'])->select('id, ulica, dom, dom_szczegol, lokal, lokal_szczegol');
    }
    
    public function getType() {
    
        return $this->hasOne(TaskCategory::class, ['id' => 'type_id']);
    }

    public function getCategory() {
    
        return $this->hasOne(TaskCategory::class, ['id' => 'category_id']);
    }

    public function getSubcategory() {
    
        return $this->hasOne(TaskCategory::class, ['id' => 'subcategory_id']);
    }

    public function getCreateBy() {
    
        return $this->hasOne(User::class, ['id' =>'create_by']);
    }
    
    public function getCloseBy() {
    
    	return $this->hasOne(User::class, ['id' =>'close_by']);
    }
    
    public function getComments() {
    	
    	return $this->hasMany(Comment::class, ['task_id' => 'id']);
    }

    public function getConnections() {
        
        return $this->hasMany(Connection::class, ['id' => 'connection_id'])->viaTable('connection_task', ['task_id' => 'id']);
    }
    
    public function getProgramme() {
        
        return $this->start_at && $this->end_at ? true : false;
    }
}
