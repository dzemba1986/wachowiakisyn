<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalTerminator;

class OpticalTerminatorController extends DeviceController {
    
    protected static function classNameModel() {
        
        return OpticalTerminator::class;
    }
}
