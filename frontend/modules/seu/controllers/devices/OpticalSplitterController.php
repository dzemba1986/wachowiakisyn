<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalSplitter;

class OpticalSplitterController extends DeviceController {
    
    protected static function classNameModel() {
        
        return OpticalSplitter::className();
    }
}
