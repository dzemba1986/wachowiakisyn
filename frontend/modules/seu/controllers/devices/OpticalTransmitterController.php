<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalTransmitter;

class OpticalTransmitterController extends DeviceController {
    
    protected static function getModelClassName() {
        
        return OpticalTransmitter::className();
    }
    
    protected static function getModel() {
        
        return new OpticalTransmitter();
    }
}
