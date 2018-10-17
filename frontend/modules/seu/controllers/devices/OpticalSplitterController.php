<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\OpticalSplitter;

class OpticalSplitterController extends DeviceController {
    
    protected static function getModelClassName() {
        
        return OpticalSplitter::className();
    }
    
    protected static function getModel() {
        
        return new OpticalSplitter();
    }
}
