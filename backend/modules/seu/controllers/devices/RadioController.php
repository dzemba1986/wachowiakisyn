<?php

namespace backend\modules\seu\controllers\devices;

use common\models\seu\devices\Radio;

class RadioController extends DeviceController {
    
    protected static function getModel() {
        
        return new Radio();
    }
}
