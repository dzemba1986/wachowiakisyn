<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;

class SelfTask extends Task {
    
    const TYPE = 7;
    
    public static function find() {
        
        return new TaskQuery(get_called_class(), ['type_id' => self::TYPE, 'columns' => self::columns()]);
    }
}