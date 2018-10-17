<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\MediaConverter;

class MediaConverterController extends DeviceController {
    
    protected static function getModel() {
        
        return new MediaConverter();
    }
}
