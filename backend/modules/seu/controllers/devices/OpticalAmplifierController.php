<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalAmplifier;

class OpticalAmplifierController extends DeviceController {
    
    protected static function getModel() {
        
        return new OpticalAmplifier();
    }
}
