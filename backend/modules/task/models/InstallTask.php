<?php

namespace backend\modules\task\models;

use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * @property string $start
 * @property string $end
 * @property string $start_date
 * @property string $start_time
 * @property string $end_time
 * @property string $class_name
 * @property string $color
 * @property string $installer
 * @property string $phone
 * @property float $cost
 * @property boolean $editable
 * @property boolean $nocontract
 * @property boolean $paid_psm
 */
class InstallTask extends Task
{	
	public $street;
	public $house;
	public $house_detail;
	public $flat;
	public $flat_detail;
	public $start_date;
	public $start_time;
	public $end_time;
	public $minClose;
	public $maxClose;
	
    public function attributes(){
    	
    	return ArrayHelper::merge(
    		parent::attributes(),
    		[
    			'start', 
    			'end', 
    			'class_name', 
    			'color', 
    			'installer', 
    			'phone', 
    			'cost', 
    			'editable',
    			'nocontract',
    			'paid_psm'	
    		]
    	);
    }

    public function rules(){
    	
        return ArrayHelper::merge(
        	parent::rules(),	
        	[	
        		['start', 'date', 'format' => 'php:Y-m-d H:i:s', 'message'=>'Zły format'],
	            ['start', 'required', 'message'=>'Wartość wymagana'],
	            
        		['end', 'date', 'format' => 'php:Y-m-d H:i:s', 'message'=>'Zły format'],
	            ['end', 'required', 'message'=>'Wartość wymagana'],
	            
        		['start_date', 'date', 'format' => 'yyyy-MM-dd', 'message'=>'Zły format'],
        		['start_date', 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message'=>'Zły format'],
        		['start_date', 'required', 'message'=>'Wartość wymagana'],
        			
        		['start_time', 'date', 'format' => 'php:H:i', 'whenClient' => new JsExpression('function() {return false}')],
        		['start_time', 'match', 'pattern' => '/^(0[0-9]|1[0-9]|2[0123])\:([012345][0-9])$/u', 'message'=>'Zły format',
        			'whenClient' => new JsExpression('function() {return false}')
        		],
        		['start_time', 'required', 'message'=>'Wartość wymagana'],
        			
        		['end_time', 'date', 'format' => 'php:H:i', 'whenClient' => new JsExpression('function() {return false}')],
        		['end_time', 'match', 'pattern' => '/^(0[0-9]|1[0-9]|2[0123])\:([012345][0-9])$/u', 'message'=>'Zły format',
        			'whenClient' => new JsExpression('function() {return false}')
        		],
        		['end_time', 'required', 'message'=>'Wartość wymagana'],
        		['end_time', 'compare', 'compareAttribute' => 'start_time', 'operator' => '>', 'message' => 'Wartość > od czasu początkowego'],
        			
	            ['color', 'string'],
	        	['color', 'default', 'value' => null],
	            
	            ['class_name', 'string'],
	        	['class_name', 'default', 'value' => null],
	            
	            ['editable', 'boolean', 'trueValue' => true, 'falseValue' => false],
	        	['editable', 'default', 'value' => true],
	        	
        		['paid_psm', 'boolean'],
        		['paid_psm', 'default', 'value' => false],
        			
	            ['cost', 'double', 'message' => 'Wartość liczbowa'],
	        	['cost', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
	            
	            ['installer', 'string', 'message' => 'Wartość znakowa', 'whenClient' => new JsExpression('function() {return false}')],
	        	['installer', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
	            
	            ['phone', 'trim'],
	            ['phone', 'string', 'min'=>9, 'max'=>13, 'tooShort'=>'Minimum 9 znaków', 'tooLong'=>'Maksimum 12 znaków'],
	            
	            [['start', 'end', 'start_date', 'start_time', 'end_time', 'all_day', 'installer', 'color', 'editable', 'class_name', 'cost', 'phone', 'street'], 'safe'],
        	]
        );
    }
    
    public function scenarios()
    {
    	$scenarios = parent::scenarios();
    	array_push($scenarios[self::SCENARIO_CREATE], 'start', 'end', 'start_date', 'start_time', 'end_time', 'color', 'class_name', 'editable', 'phone', 'nocontract', 'paid_psm');
    	array_push($scenarios[self::SCENARIO_UPDATE], 'start', 'end', 'start_date', 'start_time', 'end_time', 'color', 'class_name', 'editable', 'phone', 'paid_psm');
    	array_push($scenarios[self::SCENARIO_CLOSE], 'editable', 'color', 'paid_psm', 'installer', 'cost');
    	
    	return $scenarios;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(
        	parent::attributeLabels(),	
        	[
	            'start' => 'Start',
	            'end' => 'Koniec',
	            'color' => 'Kolor',
	            'installer' => 'Wykonał',
	            'phone' => 'Telefon',
	            'cost' => 'Koszt',
        		'street' => 'Ulica',
        		'house' => 'Dom',
        		'house_detail' => 'Klatka',
        		'flat' => 'Lokal',
        		'paid_psm' => 'PSM'	
        	]
        );
    }
    
    public function afterFind(){
	    
    	$this->start_date = date('Y-m-d', strtotime($this->start));
	    $this->start_time = date('H:i', strtotime($this->start));
	    $this->end_time = date('H:i', strtotime($this->end));
	    
	    parent::afterFind();
    }
    
    public function getColor(){
    	
    	return $this->type->color;
    }
}