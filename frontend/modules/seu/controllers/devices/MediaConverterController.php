<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\MediaConverter;

class MediaConverterController extends DeviceController {
    
    protected static function getModelClassName() {
        
        return MediaConverter::className();
    }
    
    protected static function getModel() {
        
        return new MediaConverter();
    }
}
