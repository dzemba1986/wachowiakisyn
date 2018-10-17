<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalSplitter;

class OpticalSplitterController extends DeviceController {
    
    protected static function getModel() {
        
        return new OpticalSplitter();
    }
}
