<?php

namespace backend\controllers;

use backend\models\Radio;

class RadioController extends DeviceController {
    
    protected static function getModel() {
        
        return new Radio();
    }
}
