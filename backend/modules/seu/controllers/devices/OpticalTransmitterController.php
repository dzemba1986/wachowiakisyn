<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalTransmitter;

class OpticalTransmitterController extends DeviceController {
    
    protected static function getModel() {
        
        return new OpticalTransmitter();
    }
}
