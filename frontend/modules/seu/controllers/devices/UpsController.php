<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Ups;

class UpsController extends DeviceController {
    
    protected static function getModelClassName() {
        
        return Ups::className();
    }
    
    protected static function getModel() {
        
        return new Ups();
    }
}