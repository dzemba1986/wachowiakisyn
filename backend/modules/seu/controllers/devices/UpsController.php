<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\Ups;

class UpsController extends DeviceController {
    
    protected static function getModel() {
        
        return new Ups();
    }
}