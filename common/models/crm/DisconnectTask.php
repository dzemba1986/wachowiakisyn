<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * @property string $installer
 * @property string $phone
 * @property float $cost
 * @property integer $payer
 */

class DisconnectTask extends Task {
    
    const TYPE = 6;
    
	public $street;
	public $house;
	public $house_detail;
	public $flat;
	public $flat_detail;
	public $minClose;
	public $maxClose;
	public static $categoryName = [
	    1 => 'Internet',
	    2 => 'Telefon',
	    3 => 'Telewizja',
	    100 => 'Inne',
	];
	public static $labelName = [
	    1 => 'Instalacja',
	    2 => 'Kabel',
	    3 => 'Gniazdo',
	    100 => 'Inne',
	];
	public static $payerName = [
	    1 => 'Klient',
	    2 => 'WTVK',
	];
	
    public static function columns() {
    	
    	return ArrayHelper::merge(
    		parent::columns(),
    		[
    			'installer', 
    			'phone', 
    			'cost', 
    			'payer',
    		    'connection_id',
    		]
    	);
    }

    public function rules() {
    	
        return ArrayHelper::merge(
        	parent::rules(),	
        	[	
	            ['start_at', 'required', 'message'=>'Wartość wymagana'],
	            
	            ['end_at', 'required', 'message'=>'Wartość wymagana'],
	            
        	    ['day', 'required', 'message' => 'Wartość wymagana'],
        	    
        	    ['start_time', 'required', 'message' => 'Wartość wymagana'],
        			
        	    ['end_time', 'required', 'message' => 'Wartość wymagana'],
	            
        	    ['category_id', 'required', 'message' => 'Wartość wymagana'],
        	    
        	    ['receive_by', 'required', 'message' => 'Wartość wymagana'],
        	    
        		['payer', 'integer'],
        	    ['payer', 'required', 'message' => 'Wartość wymagana'],
        			
	            ['cost', 'double', 'message' => 'Wartość liczbowa'],
	        	['cost', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
	            
	            ['installer', 'string', 'message' => 'Wartość znakowa', 'whenClient' => new JsExpression('function() {return false}')],
	        	['installer', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
	            
	            ['phone', 'trim'],
	            ['phone', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
        	]
        );
    }
    
    public function scenarios() {
        
    	$scenarios = parent::scenarios();
    	array_push($scenarios[self::SCENARIO_CREATE], 'phone', 'payer', 'connection_id');
    	array_push($scenarios[self::SCENARIO_UPDATE], 'phone', 'payer');
    	array_push($scenarios[self::SCENARIO_CLOSE], 'payer', 'installer', 'cost', 'connection_id');
    	
    	return $scenarios;
    }

    public function attributeLabels() {
        
        return ArrayHelper::merge(
        	parent::attributeLabels(),	
        	[
	            'installer' => 'Wykonał',
	            'phone' => 'Telefon',
	            'cost' => 'Koszt',
        		'street' => 'Ulica',
        		'house' => 'Dom',
        		'house_detail' => 'Klatka',
        		'flat' => 'Lokal',
        		'payer' => 'Płatnik',
        	    'connection_id' => 'Umowa',
        	]
        );
    }
    
    public function beforeValidate() {
        
        if ($this->payer) $this->payer = (int) $this->payer;
        
        return parent::beforeValidate();
    }
    
    public static function find() {
        
        return new TaskQuery(get_called_class(), ['type_id' => self::TYPE, 'columns' => self::columns()]);
    }
}