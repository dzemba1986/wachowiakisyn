<?php

namespace frontend\modules\seu\controllers\devices;

use common\models\seu\devices\MediaConverter;

class MediaConverterController extends DeviceController {
    
    protected static function classNameModel() {
        
        return MediaConverter::className();
    }
}
