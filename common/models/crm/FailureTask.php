<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;
use yii\helpers\ArrayHelper;

/**
 * @property string $installer
 * @property string $phone
 * @property float $cost
 * @property integer $payer
 */

class FailureTask extends Task {
    
    const TYPE = 5;
    
	public $street;
	public $house;
	public $house_detail;
	public $flat;
	public $flat_detail;
	
    public static function columns() {
    	
    	return ArrayHelper::merge(
    		parent::columns(),
    		[
    			'phone', 
    			'cost', 
    			'payer',
    		]
    	);
    }

    public function rules() {
    	
        return ArrayHelper::merge(
        	parent::rules(),	
        	[	
	            
        	    ['category_id', 'required', 'message' => 'Wartość wymagana'],
        	    
        	    ['receive_by', 'required', 'message' => 'Wartość wymagana'],
        	    
        		['payer', 'integer'],
        			
	            ['cost', 'double', 'message' => 'Wartość liczbowa'],
	            
	            ['phone', 'trim'],
	            ['phone', 'string', 'min' => 9, 'max' => 13, 'tooShort' => 'Min. {min} znaków', 'tooLong' => 'Max. {max} znaków'],
        	]
        );
    }
    
    public function scenarios() {
        
    	$scenarios = parent::scenarios();
    	array_push($scenarios[self::SCENARIO_CREATE], 'phone', 'payer');
    	array_push($scenarios[self::SCENARIO_UPDATE], 'phone', 'payer');
    	array_push($scenarios[self::SCENARIO_CLOSE], 'payer', 'cost');
    	
    	return $scenarios;
    }

    public function attributeLabels() {
        
        return ArrayHelper::merge(
        	parent::attributeLabels(),	
        	[
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