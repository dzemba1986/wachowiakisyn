<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\Radio;

class RadioController extends DeviceController {
    
    protected static function getModelClassName() {
        
        return Radio::className();
    }
    
    protected static function getModel() {
        
        return new Radio();
    }
}
