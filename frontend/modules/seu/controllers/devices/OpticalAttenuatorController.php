<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalAttenuator;

class OpticalAttenuatorController extends DeviceController {
    
    protected static function classNameModel() {
        
        return OpticalAttenuator::class;
    }
}
