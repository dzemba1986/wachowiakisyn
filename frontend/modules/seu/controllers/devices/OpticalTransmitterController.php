<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalTransmitter;

class OpticalTransmitterController extends DeviceController {
    
    protected static function classNameModel() {
        
        return OpticalTransmitter::className();
    }
}
