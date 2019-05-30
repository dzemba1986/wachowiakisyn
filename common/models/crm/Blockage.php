<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;
use yii\helpers\ArrayHelper;

class Blockage extends Task {
    
    const TYPE = 8;
    
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
            ]
        );
    }
    
    public static function find() {
        
        return new TaskQuery(get_called_class(), ['type_id' => self::TYPE, 'columns' => self::columns()]);
    }
}