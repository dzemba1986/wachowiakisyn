<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Ups;

class UpsController extends DeviceController {
    
    protected static function classNameModel() {
        
        return Ups::className();
    }
}