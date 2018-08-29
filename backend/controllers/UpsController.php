<?php

namespace backend\controllers;

use backend\models\Ups;

class UpsController extends DeviceController {
    
    protected static function getModel() {
        
        return new Ups();
    }
}