<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * @property string $done_by Who closed task (for engineer)
 * @property string $phone 
 * @property float $cost
 * @property integer $pay_by Payer: 1 -> klient; 2 -> wtvk
 * @property integer $connection_id
 * @property string $wire_at
 * @property string $wire_by
 * @property integer $wire_lenght
 * @property string $socket_at
 * @property string $socket_by
 * @property boolean $install it task do install?
 * @property boolean $again Is it reinstall?
 * @property integer $install_type
 */

class InstallTask extends Task {
    
    const TYPE = 3;
    const CONTROLLER = 'install-task';
    
    public static function columns() {
    	
    	return ArrayHelper::merge(
    		parent::columns(),
    		[
    		    'wire_at',
    		    'wire_by',
    		    'wire_length',
    		    'socket_at',
    		    'socket_by',
    		    'install',
    		    'install_again',
    		    'cost', //koszt
    		    'pay_by', //1 - klient, 2 - WTVK
    			'done_by', //kto wykonał montaż [string]
    			'phone', 
    		]
    	);
    }

    public function rules() {
    	
        return ArrayHelper::merge(
        	parent::rules(),	
        	[	
        	    ['address_id', 'required', 'message' => 'Wartość wymagana'],

        	    ['category_id', 'required', 'message' => 'Wartość wymagana'],
        	    
        		['pay_by', 'integer'], //TODO przy montażach instalacji w ramach umowy płatnikiem musi być WTVK
        	    ['pay_by', 'required', 'message' => 'Wartość wymagana'],
        	    ['pay_by', 'filter', 'filter' => 'intval'],
        			
	            ['cost', 'double', 'message' => 'Wartość liczbowa'],
	        	['cost', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
	            
	            ['done_by', 'string', 'message' => 'Wartość znakowa', 'whenClient' => new JsExpression('function() {return false}')],
	        	['done_by', 'required', 'message' => 'Wartość wymagana', 'on' => self::SCENARIO_CLOSE],	
	            
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
	            'phone' => 'Telefon',
	            'cost' => 'Koszt',
        		'pay_by' => 'Płatnik',
        		'done_by' => 'Wykonał',
        	]
        );
    }
    
    public function behaviors() {
        
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                [
                    'class' => BlameableBehavior::class,
                    'attributes' => [
                        self::EVENT_BEFORE_INSERT => ['create_by'],
                        self::EVENT_CLOSE_TASK => ['close_by'],
                    ],
                    'value' => \Yii::$app->user->id,
                ],
            ]
        );
    }
    
//     public function beforeValidate() {
        
//         if ($this->payer) $this->payer = (int) $this->payer;
        
//         return parent::beforeValidate();
//     }
    
    public static function find() {
        
        return new TaskQuery(get_called_class(), ['type_id' => self::TYPE, 'columns' => self::columns()]);
    }
    
    public function actions() {
        
    }
}